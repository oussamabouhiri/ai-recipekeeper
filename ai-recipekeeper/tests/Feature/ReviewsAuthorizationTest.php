<?php

namespace Tests\Feature;

use App\Models\Avis;
use App\Models\Recette;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewsAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_create_a_review(): void
    {
        $recipe = Recette::factory()->create();

        $this->postJson(route('api.reviews.store', $recipe), ['rating' => 4])
            ->assertUnauthorized();

        $this->assertDatabaseCount('avis', 0);
    }

    public function test_guest_cannot_update_a_review(): void
    {
        $avis = Avis::factory()->create();

        $this->putJson(route('api.reviews.update', $avis), ['rating' => 1])
            ->assertUnauthorized();

        $this->assertDatabaseHas('avis', ['id' => $avis->id, 'rating' => $avis->rating]);
    }

    public function test_guest_cannot_delete_a_review(): void
    {
        $avis = Avis::factory()->create();

        $this->deleteJson(route('api.reviews.destroy', $avis))
            ->assertUnauthorized();

        $this->assertDatabaseHas('avis', ['id' => $avis->id]);
    }

    public function test_guest_can_list_reviews_of_a_visible_recipe(): void
    {
        $recipe = Recette::factory()->create();
        Avis::factory()->count(2)->create(['recette_id' => $recipe->id]);

        $this->getJson(route('api.reviews.index', $recipe))
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }

    public function test_non_owner_cannot_update_a_review(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $avis = Avis::factory()->create(['user_id' => $owner->id, 'rating' => 4]);

        $this->actingAs($other)
            ->putJson(route('api.reviews.update', $avis), ['rating' => 1])
            ->assertForbidden();

        $this->assertDatabaseHas('avis', ['id' => $avis->id, 'rating' => 4]);
    }

    public function test_non_owner_cannot_delete_a_review(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $avis = Avis::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($other)
            ->deleteJson(route('api.reviews.destroy', $avis))
            ->assertForbidden();

        $this->assertDatabaseHas('avis', ['id' => $avis->id]);
    }

    public function test_admin_can_update_any_review(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $avis = Avis::factory()->create(['user_id' => $owner->id, 'rating' => 2]);

        $this->actingAs($admin)
            ->putJson(route('api.reviews.update', $avis), ['rating' => 5])
            ->assertOk();

        $this->assertDatabaseHas('avis', ['id' => $avis->id, 'rating' => 5]);
    }

    public function test_admin_can_delete_any_review(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $avis = Avis::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($admin)
            ->deleteJson(route('api.reviews.destroy', $avis))
            ->assertNoContent();

        $this->assertDatabaseMissing('avis', ['id' => $avis->id]);
    }

    public function test_hidden_recipe_of_another_user_returns_404_for_listing(): void
    {
        $user = User::factory()->create();
        $hidden = Recette::factory()->hidden()->create();

        $this->actingAs($user)
            ->getJson(route('api.reviews.index', $hidden))
            ->assertNotFound();
    }

    public function test_hidden_recipe_of_another_user_returns_404_for_creation(): void
    {
        $user = User::factory()->create();
        $hidden = Recette::factory()->hidden()->create();

        $this->actingAs($user)
            ->postJson(route('api.reviews.store', $hidden), ['rating' => 4])
            ->assertNotFound();

        $this->assertDatabaseCount('avis', 0);
    }
}
