<?php

namespace Tests\Feature;

use App\Models\Ingredient;
use App\Models\Recette;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeValidationTest extends TestCase
{
    use RefreshDatabase;

    private function actingAsUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        return $user;
    }

    public function test_missing_title_is_rejected(): void
    {
        $this->actingAsUser();

        $this->postJson(route('api.recipes.store'), ['title' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('title');
    }

    public function test_invalid_status_is_rejected(): void
    {
        $this->actingAsUser();

        $this->postJson(route('api.recipes.store'), [
            'title' => 'Recette',
            'statut' => 'archived',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('statut');
    }

    public function test_unknown_ingredient_is_rejected(): void
    {
        $this->actingAsUser();

        $this->postJson(route('api.recipes.store'), [
            'title' => 'Recette',
            'ingredients' => [
                ['ingredient_id' => 9999, 'quantity' => '1', 'unit' => 'g'],
            ],
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('ingredients.0.ingredient_id');
    }

    public function test_step_without_positive_step_number_is_rejected(): void
    {
        $this->actingAsUser();

        $this->postJson(route('api.recipes.store'), [
            'title' => 'Recette',
            'etapes' => [
                ['step_number' => 0, 'instruction' => 'Étape'],
            ],
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('etapes.0.step_number');
    }

    public function test_step_without_instruction_is_rejected(): void
    {
        $this->actingAsUser();

        $this->postJson(route('api.recipes.store'), [
            'title' => 'Recette',
            'etapes' => [
                ['step_number' => 1],
            ],
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('etapes.0.instruction');
    }

    public function test_negative_times_are_rejected(): void
    {
        $this->actingAsUser();

        $this->postJson(route('api.recipes.store'), [
            'title' => 'Recette',
            'prep_time' => -5,
            'cook_time' => -10,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['prep_time', 'cook_time']);
    }

    public function test_servings_below_one_is_rejected(): void
    {
        $this->actingAsUser();

        $this->postJson(route('api.recipes.store'), [
            'title' => 'Recette',
            'servings' => 0,
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('servings');
    }

    public function test_update_payload_is_validated(): void
    {
        $owner = $this->actingAsUser();
        $recipe = Recette::factory()->create(['user_id' => $owner->id]);

        $this->putJson(route('api.recipes.update', $recipe), ['title' => ''])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('title');
    }

    public function test_unknown_category_is_rejected(): void
    {
        $this->actingAsUser();

        $this->postJson(route('api.recipes.store'), [
            'title' => 'Recette',
            'categories' => [9999],
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('categories.0');
    }

    public function test_valid_ingredient_is_accepted(): void
    {
        $this->actingAsUser();
        $ingredient = Ingredient::create(['name' => 'Lait']);

        $this->postJson(route('api.recipes.store'), [
            'title' => 'Recette',
            'ingredients' => [
                ['ingredient_id' => $ingredient->id, 'quantity' => '1', 'unit' => 'L'],
            ],
        ])
            ->assertCreated();
    }
}
