<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Recette;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeBladeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('recipes.index'))
            ->assertRedirect(route('login'));

        $this->get(route('recipes.create'))
            ->assertRedirect(route('login'));

        $recipe = Recette::factory()->create();
        $this->get(route('recipes.show', $recipe))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_see_recipe_list(): void
    {
        $user = User::factory()->create();
        $published = Recette::factory()->create(['statut' => 'published', 'user_id' => $user->id]);
        $hidden = Recette::factory()->hidden()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('recipes.index'))
            ->assertOk()
            ->assertSee($published->title)
            ->assertSee($hidden->title);
    }

    public function test_user_does_not_see_others_hidden_recipes(): void
    {
        $user = User::factory()->create();
        $otherHidden = Recette::factory()->hidden()->create();

        $this->actingAs($user)
            ->get(route('recipes.index'))
            ->assertOk()
            ->assertDontSee($otherHidden->title);
    }

    public function test_authenticated_user_can_view_published_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create(['statut' => 'published']);

        $this->actingAs($user)
            ->get(route('recipes.show', $recipe))
            ->assertOk()
            ->assertSee($recipe->title);
    }

    public function test_owner_can_view_own_hidden_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->hidden()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('recipes.show', $recipe))
            ->assertOk()
            ->assertSee($recipe->title);
    }

    public function test_non_owner_gets_404_for_hidden_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->hidden()->create();

        $this->actingAs($user)
            ->get(route('recipes.show', $recipe))
            ->assertNotFound();
    }

    public function test_authenticated_user_can_access_create_form(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('recipes.create'))
            ->assertOk()
            ->assertSee('Create Recipe');
    }

    public function test_successful_recipe_creation(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Plat principal']);
        $ingredient = Ingredient::create(['name' => 'Farine']);

        $this->actingAs($user)
            ->post(route('recipes.store'), [
                'title' => 'Crêpes maison',
                'description' => 'Une recette simple.',
                'prep_time' => 10,
                'cook_time' => 20,
                'servings' => 4,
                'difficulty' => 'Easy',
                'statut' => 'published',
                'etapes' => [
                    ['step_number' => 1, 'instruction' => 'Mélanger la farine et le lait.'],
                    ['step_number' => 2, 'instruction' => 'Cuire dans une poêle.'],
                ],
                'ingredients' => [
                    ['ingredient_id' => $ingredient->id, 'quantity' => '250', 'unit' => 'g'],
                ],
                'categories' => [$category->id],
            ])
            ->assertRedirect();

        $recipe = Recette::query()->firstOrFail();
        $this->assertSame('Crêpes maison', $recipe->title);
        $this->assertSame($user->id, $recipe->user_id);
        $this->assertDatabaseCount('etapes', 2);
        $this->assertDatabaseHas('recette_ingredient', [
            'recette_id' => $recipe->id,
            'ingredient_id' => $ingredient->id,
        ]);
    }

    public function test_validation_errors_on_invalid_create(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('recipes.store'), [])
            ->assertSessionHasErrors('title');
    }

    public function test_owner_can_access_edit_form(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('recipes.edit', $recipe))
            ->assertOk()
            ->assertSee('Edit Recipe')
            ->assertSee($recipe->title);
    }

    public function test_non_owner_gets_403_for_edit(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();

        $this->actingAs($user)
            ->get(route('recipes.edit', $recipe))
            ->assertForbidden();
    }

    public function test_successful_recipe_update(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create(['user_id' => $user->id]);
        $recipe->etapes()->create(['step_number' => 1, 'instruction' => 'Ancienne étape']);

        $this->actingAs($user)
            ->put(route('recipes.update', $recipe), [
                'title' => 'Titre modifié',
                'description' => 'Description modifiée',
                'etapes' => [
                    ['step_number' => 1, 'instruction' => 'Nouvelle étape'],
                ],
            ])
            ->assertRedirect(route('recipes.show', $recipe));

        $this->assertDatabaseHas('recettes', [
            'id' => $recipe->id,
            'title' => 'Titre modifié',
        ]);
    }

    public function test_owner_can_delete_own_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->delete(route('recipes.destroy', $recipe))
            ->assertRedirect(route('recipes.index'));

        $this->assertDatabaseMissing('recettes', ['id' => $recipe->id]);
    }

    public function test_non_owner_gets_403_for_delete(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();

        $this->actingAs($user)
            ->delete(route('recipes.destroy', $recipe))
            ->assertForbidden();
    }

    public function test_admin_can_view_edit_delete_any_recipe(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $recipe = Recette::factory()->create();

        $this->actingAs($admin)
            ->get(route('recipes.show', $recipe))
            ->assertOk();

        $this->actingAs($admin)
            ->get(route('recipes.edit', $recipe))
            ->assertOk();

        $this->actingAs($admin)
            ->delete(route('recipes.destroy', $recipe))
            ->assertRedirect(route('recipes.index'));

        $this->assertDatabaseMissing('recettes', ['id' => $recipe->id]);
    }

    public function test_recipe_detail_shows_full_recipe_data(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create();
        $category = Category::factory()->create(['name' => 'Pescatarian']);
        $ingredient = Ingredient::create(['name' => 'Salmon', 'unit' => 'fillets']);

        $recipe->categories()->attach($category);
        $recipe->ingredients()->attach($ingredient, ['quantity' => '4', 'unit' => 'fillets']);
        $recipe->etapes()->createMany([
            ['step_number' => 1, 'instruction' => 'Preheat the oven.'],
            ['step_number' => 2, 'instruction' => 'Season the salmon.'],
        ]);

        $this->actingAs($user)
            ->get(route('recipes.show', $recipe))
            ->assertOk()
            ->assertSee($recipe->title)
            ->assertSeeInOrder(['Prep', $recipe->prep_time . ' min'])
            ->assertSeeInOrder(['Cook', $recipe->cook_time . ' min'])
            ->assertSeeInOrder(['Servings', (string) $recipe->servings])
            ->assertSeeInOrder(['Difficulty', $recipe->difficulty])
            ->assertSee('Ingredients')
            ->assertSee('Salmon')
            ->assertSee('fillets')
            ->assertSee('Preparation Steps')
            ->assertSee('01')
            ->assertSee('Preheat the oven.')
            ->assertSee('Pescatarian');
    }

    public function test_recipe_detail_shows_fallback_and_no_reviews_state(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create(['image_path' => null]);

        $this->actingAs($user)
            ->get(route('recipes.show', $recipe))
            ->assertOk()
            ->assertSee('No reviews yet');
    }

    public function test_owner_sees_hidden_badge_on_own_hidden_recipe(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->hidden()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('recipes.show', $recipe))
            ->assertOk()
            ->assertSee('Hidden')
            ->assertDontSee('Published');
    }
}
