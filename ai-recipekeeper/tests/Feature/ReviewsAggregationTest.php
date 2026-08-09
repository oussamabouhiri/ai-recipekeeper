<?php

namespace Tests\Feature;

use App\Models\Avis;
use App\Models\Recette;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewsAggregationTest extends TestCase
{
    use RefreshDatabase;

    public function test_list_response_includes_average_rating_and_count(): void
    {
        $recipe = Recette::factory()->create();
        Avis::factory()->create(['recette_id' => $recipe->id, 'rating' => 4]);
        Avis::factory()->create(['recette_id' => $recipe->id, 'rating' => 5]);

        $this->getJson(route('api.reviews.index', $recipe))
            ->assertOk()
            ->assertJsonPath('rating_avg', 4.5)
            ->assertJsonPath('rating_count', 2);
    }

    public function test_recipe_without_reviews_returns_null_average_and_zero_count(): void
    {
        $recipe = Recette::factory()->create();

        $this->getJson(route('api.reviews.index', $recipe))
            ->assertOk()
            ->assertJsonPath('rating_avg', null)
            ->assertJsonPath('rating_count', 0);
    }

    public function test_aggregates_only_cover_the_recipe_own_reviews(): void
    {
        $recipe = Recette::factory()->create();
        $other = Recette::factory()->create();
        Avis::factory()->create(['recette_id' => $other->id, 'rating' => 1]);

        $this->getJson(route('api.reviews.index', $recipe))
            ->assertOk()
            ->assertJsonPath('rating_avg', null)
            ->assertJsonPath('rating_count', 0);
    }
}
