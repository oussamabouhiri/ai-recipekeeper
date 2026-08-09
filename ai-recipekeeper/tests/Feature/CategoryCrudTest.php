<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Recette;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_listing_returns_all_categories(): void
    {
        $categories = Category::factory()->count(3)->create();

        $this->getJson(route('api.categories.index'))
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_guest_can_view_a_category(): void
    {
        $category = Category::factory()->create();

        $this->getJson(route('api.categories.show', $category))
            ->assertOk()
            ->assertJsonPath('data.id', $category->id)
            ->assertJsonPath('data.name', $category->name);
    }

    public function test_admin_can_create_a_category(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson(route('api.categories.store'), [
            'name' => 'Plat principal',
            'description' => 'Les plats principaux',
        ])
            ->assertCreated()
            ->assertJsonPath('data.name', 'Plat principal')
            ->assertJsonPath('data.description', 'Les plats principaux');

        $this->assertDatabaseHas('categories', ['name' => 'Plat principal']);
    }

    public function test_admin_can_update_a_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Ancien nom']);

        $this->actingAs($admin)->putJson(route('api.categories.update', $category), [
            'name' => 'Nouveau nom',
        ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Nouveau nom');

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Nouveau nom']);
    }

    public function test_admin_can_delete_a_category(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $this->actingAs($admin)->deleteJson(route('api.categories.destroy', $category))
            ->assertNoContent();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_deleting_category_removes_pivot_but_not_recipes(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $recipe = Recette::factory()->create();
        $recipe->categories()->attach($category->id);

        $this->assertDatabaseHas('recette_categorie', [
            'recette_id' => $recipe->id,
            'categorie_id' => $category->id,
        ]);

        $this->actingAs($admin)->deleteJson(route('api.categories.destroy', $category))
            ->assertNoContent();

        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
        $this->assertDatabaseMissing('recette_categorie', ['categorie_id' => $category->id]);
        $this->assertDatabaseHas('recettes', ['id' => $recipe->id]);
    }
}
