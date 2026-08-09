<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recette;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_listing_returns_only_published_recipes(): void
    {
        $published = Recette::factory()->create();
        $hidden = Recette::factory()->hidden()->create();

        $this->getJson(route('api.recipes.index'))
            ->assertOk()
            ->assertJsonFragment(['id' => $published->id])
            ->assertJsonMissing(['id' => $hidden->id]);
    }

    public function test_guest_cannot_create_a_recipe(): void
    {
        $this->postJson(route('api.recipes.store'), ['title' => 'Sans compte'])
            ->assertUnauthorized();
    }

    public function test_authenticated_user_can_create_a_recipe_with_relationships(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Plat principal']);
        $ingredient = Ingredient::create(['name' => 'Farine']);

        $response = $this->actingAs($user)->postJson(route('api.recipes.store'), [
            'title' => 'Crêpes maison',
            'description' => 'Une recette simple.',
            'prep_time' => 10,
            'cook_time' => 20,
            'servings' => 4,
            'etapes' => [
                ['step_number' => 1, 'instruction' => 'Mélanger la farine et le lait.'],
                ['step_number' => 2, 'instruction' => 'Cuire dans une poêle.'],
            ],
            'ingredients' => [
                ['ingredient_id' => $ingredient->id, 'quantity' => '250', 'unit' => 'g'],
            ],
            'categories' => [$category->id],
        ]);

        $response
            ->assertCreated()
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('data.statut', 'published')
            ->assertJsonPath('data.etapes.0.step_number', 1)
            ->assertJsonPath('data.ingredients.0.quantity', '250');

        $recipe = Recette::query()->firstOrFail();

        $this->assertSame('Crêpes maison', $recipe->title);
        $this->assertSame($user->id, $recipe->user_id);
        $this->assertSame('published', $recipe->statut);
        $this->assertFalse($recipe->is_ai_generated);
        $this->assertDatabaseCount('etapes', 2);
        $this->assertDatabaseHas('recette_ingredient', [
            'recette_id' => $recipe->id,
            'ingredient_id' => $ingredient->id,
            'quantity' => '250',
            'unit' => 'g',
        ]);
        $this->assertDatabaseHas('recette_categorie', [
            'recette_id' => $recipe->id,
            'categorie_id' => $category->id,
        ]);
    }

    public function test_guest_can_view_a_published_recipe(): void
    {
        $recipe = Recette::factory()->create();

        $this->getJson(route('api.recipes.show', $recipe))
            ->assertOk()
            ->assertJsonPath('data.id', $recipe->id)
            ->assertJsonPath('data.statut', 'published');
    }

    public function test_owner_can_update_own_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create(['user_id' => $user->id]);
        $recipe->etapes()->create(['step_number' => 1, 'instruction' => 'Ancienne étape']);
        $oldIngredient = Ingredient::create(['name' => 'Ancien ingrédient']);
        $recipe->ingredients()->attach($oldIngredient->id, ['quantity' => '1', 'unit' => 'pièce']);
        $newIngredient = Ingredient::create(['name' => 'Nouvel ingrédient']);

        $this->actingAs($user)->putJson(route('api.recipes.update', $recipe), [
            'title' => 'Titre modifié',
            'etapes' => [
                ['step_number' => 1, 'instruction' => 'Nouvelle étape'],
            ],
            'ingredients' => [
                ['ingredient_id' => $newIngredient->id, 'quantity' => '2', 'unit' => 'g'],
            ],
            'categories' => [],
        ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Titre modifié');

        $this->assertDatabaseHas('etapes', [
            'recette_id' => $recipe->id,
            'instruction' => 'Nouvelle étape',
        ]);
        $this->assertDatabaseMissing('etapes', ['instruction' => 'Ancienne étape']);
        $this->assertDatabaseMissing('recette_ingredient', ['ingredient_id' => $oldIngredient->id]);
        $this->assertDatabaseHas('recette_ingredient', [
            'recette_id' => $recipe->id,
            'ingredient_id' => $newIngredient->id,
            'quantity' => '2',
            'unit' => 'g',
        ]);
    }

    public function test_owner_can_delete_own_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create(['user_id' => $user->id]);
        $recipe->etapes()->create(['step_number' => 1, 'instruction' => 'Étape']);

        $this->actingAs($user)->deleteJson(route('api.recipes.destroy', $recipe))
            ->assertNoContent();

        $this->assertDatabaseMissing('recettes', ['id' => $recipe->id]);
        $this->assertDatabaseCount('etapes', 0);
    }

    public function test_guest_cannot_update_or_delete_a_recipe(): void
    {
        $recipe = Recette::factory()->create();

        $this->putJson(route('api.recipes.update', $recipe), ['title' => 'Volé'])
            ->assertUnauthorized();

        $this->deleteJson(route('api.recipes.destroy', $recipe))
            ->assertUnauthorized();
    }
}
