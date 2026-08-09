<?php

namespace Tests\Feature;

use App\Models\Avis;
use App\Models\Recette;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReviewsBladeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $recipe = Recette::factory()->create();
        $avis = Avis::factory()->create();

        $this->post(route('reviews.store', $recipe), ['rating' => 4])
            ->assertRedirect(route('login'));

        $this->put(route('reviews.update', $avis), ['rating' => 5])
            ->assertRedirect(route('login'));

        $this->delete(route('reviews.destroy', $avis))
            ->assertRedirect(route('login'));
    }

    public function test_recipe_detail_shows_rating_summary_and_reviews(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create(['name' => 'Alice Chef']);
        $recipe = Recette::factory()->create();
        Avis::factory()->create(['user_id' => $author->id, 'recette_id' => $recipe->id, 'rating' => 4]);
        Avis::factory()->create(['user_id' => $author->id, 'recette_id' => $recipe->id, 'rating' => 5, 'comment' => 'Delicious!']);

        $this->actingAs($user)
            ->get(route('recipes.show', $recipe))
            ->assertOk()
            ->assertSee('4.5 / 5')
            ->assertSee('2 reviews')
            ->assertSee('Alice Chef')
            ->assertSee('Delicious!');
    }

    public function test_recipe_detail_shows_create_form_when_user_has_no_review(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();

        $this->actingAs($user)
            ->get(route('recipes.show', $recipe))
            ->assertOk()
            ->assertSee('Submit Review')
            ->assertDontSee('Update Review');
    }

    public function test_recipe_detail_shows_edit_and_delete_for_own_review(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();
        Avis::factory()->create(['user_id' => $user->id, 'recette_id' => $recipe->id, 'rating' => 3]);

        $this->actingAs($user)
            ->get(route('recipes.show', $recipe))
            ->assertOk()
            ->assertSee('Edit Review')
            ->assertSee('Update Review')
            ->assertSee('Delete Review')
            ->assertDontSee('Submit Review');
    }

    public function test_admin_sees_delete_action_on_every_review(): void
    {
        $admin = User::factory()->admin()->create();
        $author = User::factory()->create(['name' => 'Bob Baker']);
        $recipe = Recette::factory()->create();
        Avis::factory()->create(['user_id' => $author->id, 'recette_id' => $recipe->id, 'rating' => 2]);

        $this->actingAs($admin)
            ->get(route('recipes.show', $recipe))
            ->assertOk()
            ->assertSee('Bob Baker')
            ->assertSee('Delete Review');
    }

    public function test_non_owner_does_not_see_delete_action_on_others_reviews(): void
    {
        $user = User::factory()->create();
        $author = User::factory()->create();
        $recipe = Recette::factory()->create();
        Avis::factory()->create(['user_id' => $author->id, 'recette_id' => $recipe->id, 'rating' => 4]);

        $this->actingAs($user)
            ->get(route('recipes.show', $recipe))
            ->assertOk()
            ->assertDontSee('Delete Review');
    }

    public function test_user_can_create_review_from_recipe_detail(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();

        $this->actingAs($user)
            ->from(route('recipes.show', $recipe))
            ->post(route('reviews.store', $recipe), [
                'rating' => 4,
                'comment' => 'Very good!',
            ])
            ->assertRedirect(route('recipes.show', $recipe))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('avis', [
            'user_id' => $user->id,
            'recette_id' => $recipe->id,
            'rating' => 4,
            'comment' => 'Very good!',
        ]);
    }

    public function test_owner_can_update_review_from_recipe_detail(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();
        $avis = Avis::factory()->create(['user_id' => $user->id, 'recette_id' => $recipe->id, 'rating' => 2]);

        $this->actingAs($user)
            ->from(route('recipes.show', $recipe))
            ->put(route('reviews.update', $avis), [
                'rating' => 5,
                'comment' => 'Changed my mind.',
            ])
            ->assertRedirect(route('recipes.show', $recipe))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('avis', [
            'id' => $avis->id,
            'rating' => 5,
            'comment' => 'Changed my mind.',
        ]);
    }

    public function test_owner_can_delete_review_from_recipe_detail(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();
        $avis = Avis::factory()->create(['user_id' => $user->id, 'recette_id' => $recipe->id]);

        $this->actingAs($user)
            ->from(route('recipes.show', $recipe))
            ->delete(route('reviews.destroy', $avis))
            ->assertRedirect(route('recipes.show', $recipe))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('avis', ['id' => $avis->id]);
    }

    public function test_invalid_review_form_redisplays_with_validation_errors(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();

        $this->actingAs($user)
            ->from(route('recipes.show', $recipe))
            ->post(route('reviews.store', $recipe), ['comment' => 'Missing rating'])
            ->assertRedirect(route('recipes.show', $recipe))
            ->assertSessionHasErrors('rating');

        $this->assertDatabaseCount('avis', 0);
    }
}
