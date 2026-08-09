<?php

namespace Tests\Feature;

use App\Models\Favori;
use App\Models\Recette;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_a_recipe_to_favorites(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();

        $this->actingAs($user)
            ->postJson(route('api.favorites.store'), ['recette_id' => $recipe->id])
            ->assertCreated()
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.recette_id', $recipe->id);

        $this->assertDatabaseHas('favoris', [
            'user_id' => $user->id,
            'recette_id' => $recipe->id,
        ]);
    }

    public function test_user_lists_only_own_favorites(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $own = Recette::factory()->create();
        $others = Recette::factory()->create();

        Favori::factory()->create(['user_id' => $user->id, 'recette_id' => $own->id]);
        Favori::factory()->create(['user_id' => $other->id, 'recette_id' => $others->id]);

        $this->actingAs($user)
            ->getJson(route('api.favorites.index'))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.recette_id', $own->id);
    }

    public function test_favorite_response_includes_the_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create(['title' => 'Recette Favorite']);

        $favori = Favori::factory()->create(['user_id' => $user->id, 'recette_id' => $recipe->id]);

        $this->actingAs($user)
            ->getJson(route('api.favorites.index'))
            ->assertOk()
            ->assertJsonPath('data.0.recette.id', $recipe->id)
            ->assertJsonPath('data.0.recette.title', 'Recette Favorite');
    }

    public function test_user_with_no_favorites_gets_an_empty_list(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson(route('api.favorites.index'))
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_owner_can_remove_own_favorite(): void
    {
        $user = User::factory()->create();
        $favori = Favori::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->deleteJson(route('api.favorites.destroy', $favori))
            ->assertNoContent();

        $this->assertDatabaseMissing('favoris', ['id' => $favori->id]);
    }

    public function test_recipe_deletion_cascades_favorites(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();
        $favori = Favori::factory()->create(['user_id' => $user->id, 'recette_id' => $recipe->id]);

        $recipe->delete();

        $this->assertDatabaseMissing('favoris', ['id' => $favori->id]);
    }

    public function test_user_deletion_cascades_favorites(): void
    {
        $user = User::factory()->create();
        $favori = Favori::factory()->create(['user_id' => $user->id]);

        $user->delete();

        $this->assertDatabaseMissing('favoris', ['id' => $favori->id]);
    }
}
