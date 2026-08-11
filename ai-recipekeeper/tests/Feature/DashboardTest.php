<?php

namespace Tests\Feature;

use App\Models\Avis;
use App\Models\Category;
use App\Models\Favori;
use App\Models\Recette;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_dashboard_renders_user_name_and_initials(): void
    {
        $user = User::factory()->create(['name' => 'Fatima Tester']);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Fatima Tester')
            ->assertSee('FT');
    }

    public function test_dashboard_renders_featured_recipe_real_data(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Dessert']);
        $recipe = Recette::factory()->create([
            'user_id' => $user->id,
            'title' => 'Tarte Tatin Maison',
            'description' => 'Un dessert aux pommes caramélisées.',
        ]);
        $recipe->categories()->attach($category);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Tarte Tatin Maison')
            ->assertSee('Un dessert aux pommes caramélisées.')
            ->assertSee('Dessert');
    }

    public function test_dashboard_shows_no_reviews_yet_when_recipe_has_no_reviews(): void
    {
        $user = User::factory()->create();
        Recette::factory()->create(['user_id' => $user->id]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('No reviews yet')
            ->assertSee('data-rating-count="0"', false)
            ->assertDontSee(' reviews)');
    }

    public function test_dashboard_shows_real_rating_when_reviews_exist(): void
    {
        $user = User::factory()->create();
        $recipe = Recette::factory()->create(['user_id' => $user->id]);
        Avis::factory()->create(['recette_id' => $recipe->id, 'rating' => 4]);
        Avis::factory()->create(['recette_id' => $recipe->id, 'rating' => 5]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('No reviews yet')
            ->assertSee('data-rating-avg="4.5"', false)
            ->assertSee('data-rating-count="2"', false)
            ->assertSee('(2 reviews)');
    }

    public function test_dashboard_uses_recipe_image_path(): void
    {
        $user = User::factory()->create();
        Recette::factory()->create([
            'user_id' => $user->id,
            'title' => 'Ratatouille',
            'image_path' => 'images/recipes/ratatouille.jpg',
        ]);

        $this->assertFileExists(public_path('images/recipes/ratatouille.jpg'));

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('images/recipes/ratatouille.jpg');
    }

    public function test_dashboard_renders_placeholder_when_recipe_has_no_image(): void
    {
        $user = User::factory()->create();
        Recette::factory()->create([
            'user_id' => $user->id,
            'image_path' => null,
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee('<img');
    }

    public function test_dashboard_shows_favorites_empty_state(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee("You haven't saved any favorites yet.", false);
    }

    public function test_dashboard_shows_favorited_recipes(): void
    {
        $user = User::factory()->create();
        $featured = Recette::factory()->create(['user_id' => $user->id]);
        $favoriteRecipe = Recette::factory()->create([
            'user_id' => $user->id,
            'title' => 'Mousse au Chocolat Préférée',
            'created_at' => now()->subDay(),
        ]);
        Favori::factory()->create(['user_id' => $user->id, 'recette_id' => $favoriteRecipe->id]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Mousse au Chocolat Préférée')
            ->assertSee('1 recipe saved', false)
            ->assertDontSee("You haven't saved any favorites yet.", false);
    }

    public function test_dashboard_links_to_existing_routes(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('recipes.index'))
            ->assertSee(route('generations.create'))
            ->assertSee(route('favorites.index'))
            ->assertSee(route('recipes.create'));
    }

    public function test_hidden_recipe_of_another_user_is_not_featured(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $published = Recette::factory()->create([
            'user_id' => $user->id,
            'title' => 'Recette Visible',
            'created_at' => now()->subDay(),
        ]);
        Recette::factory()->hidden()->create([
            'user_id' => $otherUser->id,
            'title' => 'Recette Cachée',
            'created_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Recette Visible')
            ->assertDontSee('Recette Cachée');

        $this->assertDatabaseHas('recettes', ['id' => $published->id, 'statut' => 'published']);
    }
}
