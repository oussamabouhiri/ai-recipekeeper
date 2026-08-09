<?php

namespace Tests\Feature;

use App\Models\Avis;
use App\Models\Recette;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewsCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_a_review(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();

        $this->actingAs($user)
            ->postJson(route('api.reviews.store', $recipe), [
                'rating' => 4,
                'comment' => 'Great recipe!',
            ])
            ->assertCreated()
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.recette_id', $recipe->id)
            ->assertJsonPath('data.rating', 4)
            ->assertJsonPath('data.comment', 'Great recipe!');

        $this->assertDatabaseHas('avis', [
            'user_id' => $user->id,
            'recette_id' => $recipe->id,
            'rating' => 4,
            'comment' => 'Great recipe!',
        ]);
    }

    public function test_user_can_review_their_own_recipe(): void
    {
        $owner = User::factory()->create();
        $recipe = Recette::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($owner)
            ->postJson(route('api.reviews.store', $recipe), ['rating' => 5])
            ->assertCreated();

        $this->assertDatabaseHas('avis', [
            'user_id' => $owner->id,
            'recette_id' => $recipe->id,
        ]);
    }

    public function test_owner_can_update_own_review(): void
    {
        $user = User::factory()->create();
        $avis = Avis::factory()->create(['user_id' => $user->id, 'rating' => 2]);

        $this->actingAs($user)
            ->putJson(route('api.reviews.update', $avis), [
                'rating' => 5,
                'comment' => 'Even better the second time!',
            ])
            ->assertOk()
            ->assertJsonPath('data.rating', 5)
            ->assertJsonPath('data.comment', 'Even better the second time!');

        $this->assertDatabaseHas('avis', [
            'id' => $avis->id,
            'rating' => 5,
        ]);
    }

    public function test_owner_can_delete_own_review(): void
    {
        $user = User::factory()->create();
        $avis = Avis::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->deleteJson(route('api.reviews.destroy', $avis))
            ->assertNoContent();

        $this->assertDatabaseMissing('avis', ['id' => $avis->id]);
    }

    public function test_same_recipe_can_be_reviewed_by_different_users(): void
    {
        $first = User::factory()->create();
        $second = User::factory()->create();
        $recipe = Recette::factory()->create();

        $this->actingAs($first)
            ->postJson(route('api.reviews.store', $recipe), ['rating' => 3])
            ->assertCreated();

        $this->actingAs($second)
            ->postJson(route('api.reviews.store', $recipe), ['rating' => 5])
            ->assertCreated();

        $this->assertDatabaseCount('avis', 2);
    }

    public function test_recipe_reviews_are_listed_with_author_names(): void
    {
        $user = User::factory()->create(['name' => 'Alice Chef']);
        $recipe = Recette::factory()->create();
        Avis::factory()->create(['user_id' => $user->id, 'recette_id' => $recipe->id, 'rating' => 5]);

        $this->getJson(route('api.reviews.index', $recipe))
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.user.name', 'Alice Chef')
            ->assertJsonPath('data.0.rating', 5);
    }

    public function test_recipe_with_no_reviews_returns_an_empty_list(): void
    {
        $recipe = Recette::factory()->create();

        $this->getJson(route('api.reviews.index', $recipe))
            ->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_recipe_deletion_cascades_reviews(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();
        $avis = Avis::factory()->create(['user_id' => $user->id, 'recette_id' => $recipe->id]);

        $recipe->delete();

        $this->assertDatabaseMissing('avis', ['id' => $avis->id]);
    }

    public function test_user_deletion_cascades_reviews(): void
    {
        $user = User::factory()->create();
        $avis = Avis::factory()->create(['user_id' => $user->id]);

        $user->delete();

        $this->assertDatabaseMissing('avis', ['id' => $avis->id]);
    }
}
