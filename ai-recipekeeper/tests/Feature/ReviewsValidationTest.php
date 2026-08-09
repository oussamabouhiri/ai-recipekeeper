<?php

namespace Tests\Feature;

use App\Models\Avis;
use App\Models\Recette;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewsValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_rating_is_rejected(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();

        $this->actingAs($user)
            ->postJson(route('api.reviews.store', $recipe), ['comment' => 'No rating here'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('rating');

        $this->assertDatabaseCount('avis', 0);
    }

    public function test_rating_below_one_is_rejected(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();

        $this->actingAs($user)
            ->postJson(route('api.reviews.store', $recipe), ['rating' => 0])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('rating');

        $this->assertDatabaseCount('avis', 0);
    }

    public function test_rating_above_five_is_rejected(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();

        $this->actingAs($user)
            ->postJson(route('api.reviews.store', $recipe), ['rating' => 6])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('rating');

        $this->assertDatabaseCount('avis', 0);
    }

    public function test_non_integer_rating_is_rejected(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();

        $this->actingAs($user)
            ->postJson(route('api.reviews.store', $recipe), ['rating' => 2.5])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('rating');

        $this->assertDatabaseCount('avis', 0);
    }

    public function test_comment_too_long_is_rejected(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();

        $this->actingAs($user)
            ->postJson(route('api.reviews.store', $recipe), [
                'rating' => 4,
                'comment' => str_repeat('a', 1001),
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('comment');

        $this->assertDatabaseCount('avis', 0);
    }

    public function test_empty_comment_creates_a_review_with_null_comment(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();

        $this->actingAs($user)
            ->postJson(route('api.reviews.store', $recipe), ['rating' => 5])
            ->assertCreated();

        $this->assertDatabaseHas('avis', [
            'user_id' => $user->id,
            'recette_id' => $recipe->id,
            'rating' => 5,
            'comment' => null,
        ]);
    }

    public function test_duplicate_review_is_rejected(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();
        Avis::factory()->create(['user_id' => $user->id, 'recette_id' => $recipe->id]);

        $this->actingAs($user)
            ->postJson(route('api.reviews.store', $recipe), ['rating' => 3])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('rating');

        $this->assertDatabaseCount('avis', 1);
    }

    public function test_owner_can_review_own_hidden_recipe(): void
    {
        $owner = User::factory()->create();
        $hidden = Recette::factory()->hidden()->create(['user_id' => $owner->id]);

        $this->actingAs($owner)
            ->postJson(route('api.reviews.store', $hidden), ['rating' => 5])
            ->assertCreated();

        $this->assertDatabaseHas('avis', [
            'user_id' => $owner->id,
            'recette_id' => $hidden->id,
        ]);
    }
}
