<?php

namespace Tests\Feature;

use App\Models\Favori;
use App\Models\Recette;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoritesAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_add_a_favorite(): void
    {
        $recipe = Recette::factory()->create();

        $this->postJson(route('api.favorites.store'), ['recette_id' => $recipe->id])
            ->assertUnauthorized();

        $this->assertDatabaseCount('favoris', 0);
    }

    public function test_guest_cannot_list_favorites(): void
    {
        $this->getJson(route('api.favorites.index'))
            ->assertUnauthorized();
    }

    public function test_guest_cannot_remove_a_favorite(): void
    {
        $favori = Favori::factory()->create();

        $this->deleteJson(route('api.favorites.destroy', $favori))
            ->assertUnauthorized();

        $this->assertDatabaseHas('favoris', ['id' => $favori->id]);
    }

    public function test_non_owner_cannot_remove_a_favorite(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $favori = Favori::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->deleteJson(route('api.favorites.destroy', $favori))
            ->assertForbidden();

        $this->assertDatabaseHas('favoris', ['id' => $favori->id]);
    }

    public function test_admin_can_remove_any_favorite(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $favori = Favori::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($admin)
            ->deleteJson(route('api.favorites.destroy', $favori))
            ->assertNoContent();

        $this->assertDatabaseMissing('favoris', ['id' => $favori->id]);
    }
}
