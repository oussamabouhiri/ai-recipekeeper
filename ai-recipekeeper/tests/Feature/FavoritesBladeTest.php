<?php

namespace Tests\Feature;

use App\Models\Favori;
use App\Models\Recette;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesBladeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('favorites.index'))
            ->assertRedirect(route('login'));

        $this->post(route('favorites.store'), ['recette_id' => 1])
            ->assertRedirect(route('login'));
    }

    public function test_my_favorites_page_shows_favorited_recipes(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create(['title' => 'Recette Favori']);
        Favori::factory()->create(['user_id' => $user->id, 'recette_id' => $recipe->id]);

        $this->actingAs($user)
            ->get(route('favorites.index'))
            ->assertOk()
            ->assertSee('Recette Favori')
            ->assertSee('Remove');
    }

    public function test_my_favorites_page_shows_empty_state(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('favorites.index'))
            ->assertOk()
            ->assertSee('No favorites yet');
    }

    public function test_recipe_detail_shows_add_favorite_button_when_not_favorited(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();

        $this->actingAs($user)
            ->get(route('recipes.show', $recipe))
            ->assertOk()
            ->assertSee('Add to Favorites')
            ->assertDontSee('Remove from Favorites');
    }

    public function test_recipe_detail_shows_remove_favorite_button_when_favorited(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();
        Favori::factory()->create(['user_id' => $user->id, 'recette_id' => $recipe->id]);

        $this->actingAs($user)
            ->get(route('recipes.show', $recipe))
            ->assertOk()
            ->assertSee('Remove from Favorites')
            ->assertDontSee('Add to Favorites');
    }

    public function test_user_can_add_favorite_from_recipe_detail(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();

        $this->actingAs($user)
            ->from(route('recipes.show', $recipe))
            ->post(route('favorites.store'), ['recette_id' => $recipe->id])
            ->assertRedirect(route('recipes.show', $recipe))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('favoris', [
            'user_id' => $user->id,
            'recette_id' => $recipe->id,
        ]);
    }

    public function test_user_can_remove_favorite_from_my_favorites_page(): void
    {
        $user = User::factory()->create();
        $favori = Favori::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->from(route('favorites.index'))
            ->delete(route('favorites.destroy', $favori))
            ->assertRedirect(route('favorites.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('favoris', ['id' => $favori->id]);
    }
}
