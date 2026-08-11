<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Favori;
use App\Models\Recette;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrowseRecipesTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_browse(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('recipes.browse'))
            ->assertOk();
    }

    public function test_browse_page_shows_published_recipes(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create([
            'user_id' => $user->id,
            'title' => 'Poulet Rôti aux Herbes',
            'statut' => 'published',
        ]);

        $this->actingAs($user)
            ->get(route('recipes.browse'))
            ->assertOk()
            ->assertSee('Poulet Rôti aux Herbes');
    }

    public function test_browse_page_hides_hidden_recipes_from_other_users(): void
    {
        $owner = User::factory()->create();
        $viewer = User::factory()->create();
        $hidden = Recette::factory()->hidden()->create([
            'user_id' => $owner->id,
            'title' => 'Recette Secrète',
        ]);

        $this->actingAs($viewer)
            ->get(route('recipes.browse'))
            ->assertOk()
            ->assertDontSee('Recette Secrète');
    }

    public function test_owner_sees_own_hidden_recipes_on_browse(): void
    {
        $user = User::factory()->create();
        $hidden = Recette::factory()->hidden()->create([
            'user_id' => $user->id,
            'title' => 'Ma Recette Cachée',
        ]);

        $this->actingAs($user)
            ->get(route('recipes.browse'))
            ->assertOk()
            ->assertSee('Ma Recette Cachée');
    }

    public function test_search_by_exact_title(): void
    {
        $user = User::factory()->create();
        Recette::factory()->create([
            'user_id' => $user->id,
            'title' => 'Risotto aux Champignons',
        ]);
        Recette::factory()->create([
            'user_id' => $user->id,
            'title' => 'Salade César',
        ]);

        $this->actingAs($user)
            ->get(route('recipes.browse', ['search' => 'Risotto aux Champignons']))
            ->assertOk()
            ->assertSee('Risotto aux Champignons')
            ->assertDontSee('Salade César');
    }

    public function test_search_by_partial_title(): void
    {
        $user = User::factory()->create();
        Recette::factory()->create([
            'user_id' => $user->id,
            'title' => 'Tarte aux Pommes',
        ]);

        $this->actingAs($user)
            ->get(route('recipes.browse', ['search' => 'Tarte']))
            ->assertOk()
            ->assertSee('Tarte aux Pommes');
    }

    public function test_search_by_description(): void
    {
        $user = User::factory()->create();
        Recette::factory()->create([
            'user_id' => $user->id,
            'title' => 'Mon Plat',
            'description' => 'Un plat délicieux au chocolat noir',
        ]);

        $this->actingAs($user)
            ->get(route('recipes.browse', ['search' => 'chocolat']))
            ->assertOk()
            ->assertSee('Mon Plat');
    }

    public function test_empty_search_returns_all_recipes(): void
    {
        $user = User::factory()->create();
        Recette::factory()->create(['user_id' => $user->id, 'title' => 'Recette A']);
        Recette::factory()->create(['user_id' => $user->id, 'title' => 'Recette B']);

        $this->actingAs($user)
            ->get(route('recipes.browse', ['search' => '']))
            ->assertOk()
            ->assertSee('Recette A')
            ->assertSee('Recette B');
    }

    public function test_search_with_no_results(): void
    {
        $user = User::factory()->create();
        Recette::factory()->create(['user_id' => $user->id, 'title' => 'Pizza']);

        $this->actingAs($user)
            ->get(route('recipes.browse', ['search' => 'xyznonexistent']))
            ->assertOk()
            ->assertSee('No recipes found');
    }

    public function test_category_filter_returns_only_matching_recipes(): void
    {
        $user = User::factory()->create();
        $dessert = Category::create(['name' => 'Dessert']);
        $soup = Category::create(['name' => 'Soupes']);

        $tarte = Recette::factory()->create(['user_id' => $user->id, 'title' => 'Tarte Tatin']);
        $tarte->categories()->attach($dessert);

        $soupe = Recette::factory()->create(['user_id' => $user->id, 'title' => 'Soupe à l\'oignon']);
        $soupe->categories()->attach($soup);

        $this->actingAs($user)
            ->get(route('recipes.browse', ['category' => $dessert->id]))
            ->assertOk()
            ->assertSee('Tarte Tatin')
            ->assertDontSee('Soupe à l\'oignon');
    }

    public function test_all_category_shows_all_recipes(): void
    {
        $user = User::factory()->create();
        Recette::factory()->create(['user_id' => $user->id, 'title' => 'Recette A']);
        Recette::factory()->create(['user_id' => $user->id, 'title' => 'Recette B']);

        $this->actingAs($user)
            ->get(route('recipes.browse'))
            ->assertOk()
            ->assertSee('Recette A')
            ->assertSee('Recette B');
    }

    public function test_search_and_category_combined(): void
    {
        $user = User::factory()->create();
        $dessert = Category::create(['name' => 'Dessert']);
        $soup = Category::create(['name' => 'Soupes']);

        $tarte = Recette::factory()->create(['user_id' => $user->id, 'title' => 'Tarte aux fruits']);
        $tarte->categories()->attach($dessert);

        $tarte_choco = Recette::factory()->create(['user_id' => $user->id, 'title' => 'Tarte au chocolat']);
        $tarte_choco->categories()->attach($dessert);

        $soupe = Recette::factory()->create(['user_id' => $user->id, 'title' => 'Soupe']);
        $soupe->categories()->attach($soup);

        $this->actingAs($user)
            ->get(route('recipes.browse', ['search' => 'Tarte', 'category' => $dessert->id]))
            ->assertOk()
            ->assertSee('Tarte aux fruits')
            ->assertSee('Tarte au chocolat')
            ->assertDontSee('Soupe à l\'oignon');
    }

    public function test_category_filter_persists_through_pagination(): void
    {
        $user = User::factory()->create();
        $dessert = Category::create(['name' => 'Dessert']);

        foreach (range(1, 13) as $i) {
            $recipe = Recette::factory()->create(['user_id' => $user->id, 'title' => "Dessert {$i}"]);
            $recipe->categories()->attach($dessert);
        }

        $this->actingAs($user)
            ->get(route('recipes.browse', ['category' => $dessert->id, 'page' => 2]))
            ->assertOk()
            ->assertSee('Dessert 13');
    }

    public function test_authenticated_user_sees_favorite_button(): void
    {
        $user = User::factory()->create();
        Recette::factory()->create(['user_id' => $user->id, 'title' => 'Mon Plat']);

        $this->actingAs($user)
            ->get(route('recipes.browse'))
            ->assertOk()
            ->assertSee('favorite');
    }

    public function test_favorited_recipe_shows_filled_heart(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create(['user_id' => $user->id]);
        Favori::create(['user_id' => $user->id, 'recette_id' => $recipe->id]);

        $this->actingAs($user)
            ->get(route('recipes.browse'))
            ->assertOk()
            ->assertSee('fill');
    }

    public function test_favorite_toggle_adds_favorite(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->post(route('favorites.store'), ['recette_id' => $recipe->id])
            ->assertRedirect();

        $this->assertDatabaseHas('favoris', [
            'user_id' => $user->id,
            'recette_id' => $recipe->id,
        ]);
    }

    public function test_favorite_toggle_removes_favorite(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create(['user_id' => $user->id]);
        $favori = Favori::create(['user_id' => $user->id, 'recette_id' => $recipe->id]);

        $this->actingAs($user)
            ->delete(route('favorites.destroy', $favori))
            ->assertRedirect();

        $this->assertDatabaseMissing('favoris', [
            'user_id' => $user->id,
            'recette_id' => $recipe->id,
        ]);
    }

    public function test_pagination_appears_with_many_recipes(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 13) as $i) {
            Recette::factory()->create(['user_id' => $user->id, 'title' => "Recipe {$i}"]);
        }

        $this->actingAs($user)
            ->get(route('recipes.browse'))
            ->assertOk()
            ->assertSee('page=2');
    }

    public function test_default_page_size_is_twelve(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 12) as $i) {
            Recette::factory()->create(['user_id' => $user->id, 'title' => "Recipe {$i}"]);
        }

        $this->actingAs($user)
            ->get(route('recipes.browse'))
            ->assertOk()
            ->assertDontSee('page=2');
    }

    public function test_browse_page_uses_dashboard_layout(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('recipes.browse'))
            ->assertOk()
            ->assertSee('Discover Your Next Meal');
    }

    public function test_browse_page_shows_categories(): void
    {
        $user = User::factory()->create();
        Category::create(['name' => 'Dessert']);
        Category::create(['name' => 'Soupes']);

        $this->actingAs($user)
            ->get(route('recipes.browse'))
            ->assertOk()
            ->assertSee('Dessert')
            ->assertSee('Soupes')
            ->assertSee('All');
    }

    public function test_browse_page_shows_search_input(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('recipes.browse'))
            ->assertOk()
            ->assertSee('Search recipes')
            ->assertSee('placeholder=');
    }

    public function test_browse_page_shows_recipe_metadata(): void
    {
        $user = User::factory()->create();
        Recette::factory()->create([
            'user_id' => $user->id,
            'title' => 'Risotto',
            'prep_time' => 15,
            'cook_time' => 30,
            'servings' => 4,
            'difficulty' => 'Moyen',
        ]);

        $this->actingAs($user)
            ->get(route('recipes.browse'))
            ->assertOk()
            ->assertSee('Risotto')
            ->assertSee('15m')
            ->assertSee('30m')
            ->assertSee('4')
            ->assertSee('Moyen');
    }
}
