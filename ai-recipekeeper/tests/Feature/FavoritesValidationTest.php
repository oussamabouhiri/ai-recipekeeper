<?php

namespace Tests\Feature;

use App\Models\Favori;
use App\Models\Recette;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_recette_id_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('api.favorites.store'), [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('recette_id');

        $this->assertDatabaseCount('favoris', 0);
    }

    public function test_unknown_recette_id_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('api.favorites.store'), ['recette_id' => 99999])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('recette_id');

        $this->assertDatabaseCount('favoris', 0);
    }

    public function test_duplicate_favorite_is_rejected(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();
        Favori::factory()->create(['user_id' => $user->id, 'recette_id' => $recipe->id]);

        $this->actingAs($user)
            ->postJson(route('api.favorites.store'), ['recette_id' => $recipe->id])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('recette_id');

        $this->assertDatabaseCount('favoris', 1);
    }

    public function test_same_recipe_can_be_favorited_by_different_users(): void
    {
        $first = User::factory()->create();
        $second = User::factory()->create();
        $recipe = Recette::factory()->create();

        $this->actingAs($first)
            ->postJson(route('api.favorites.store'), ['recette_id' => $recipe->id])
            ->assertCreated();

        $this->actingAs($second)
            ->postJson(route('api.favorites.store'), ['recette_id' => $recipe->id])
            ->assertCreated();

        $this->assertDatabaseCount('favoris', 2);
    }

    public function test_hidden_recipe_of_another_user_is_rejected(): void
    {
        $user = User::factory()->create();
        $hidden = Recette::factory()->hidden()->create();

        $this->actingAs($user)
            ->postJson(route('api.favorites.store'), ['recette_id' => $hidden->id])
            ->assertNotFound();

        $this->assertDatabaseCount('favoris', 0);
    }

    public function test_owner_can_favorite_own_hidden_recipe(): void
    {
        $owner = User::factory()->create();
        $hidden = Recette::factory()->hidden()->create(['user_id' => $owner->id]);

        $this->actingAs($owner)
            ->postJson(route('api.favorites.store'), ['recette_id' => $hidden->id])
            ->assertCreated();

        $this->assertDatabaseHas('favoris', [
            'user_id' => $owner->id,
            'recette_id' => $hidden->id,
        ]);
    }

    public function test_admin_can_favorite_any_recipe(): void
    {
        $admin = User::factory()->admin()->create();
        $hidden = Recette::factory()->hidden()->create();

        $this->actingAs($admin)
            ->postJson(route('api.favorites.store'), ['recette_id' => $hidden->id])
            ->assertCreated();

        $this->assertDatabaseHas('favoris', [
            'user_id' => $admin->id,
            'recette_id' => $hidden->id,
        ]);
    }
}
