<?php

namespace Tests\Feature;

use App\Models\Recette;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_listing_returns_only_published_recipes(): void
    {
        Recette::factory()->create();
        Recette::factory()->hidden()->create();

        $response = $this->getJson(route('api.recipes.index'))->assertOk();

        $this->assertSame(1, $response->json('meta.total'));
        $this->assertSame('published', $response->json('data.0.statut'));
    }

    public function test_owner_listing_includes_own_hidden_recipes(): void
    {
        $user = User::factory()->create();
        $ownHidden = Recette::factory()->hidden()->create(['user_id' => $user->id]);
        $othersHidden = Recette::factory()->hidden()->create();

        $response = $this->actingAs($user)->getJson(route('api.recipes.index'))->assertOk();

        $this->assertContains($ownHidden->id, collect($response->json('data'))->pluck('id')->all());
        $this->assertNotContains($othersHidden->id, collect($response->json('data'))->pluck('id')->all());
    }

    public function test_admin_listing_returns_all_recipes(): void
    {
        $admin = User::factory()->admin()->create();
        Recette::factory()->create();
        Recette::factory()->hidden()->create();

        $response = $this->actingAs($admin)->getJson(route('api.recipes.index'))->assertOk();

        $this->assertSame(2, $response->json('meta.total'));
    }

    public function test_hidden_recipe_is_not_visible_to_guest_or_other_users(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $recipe = Recette::factory()->hidden()->create(['user_id' => $owner->id]);

        $this->getJson(route('api.recipes.show', $recipe))->assertNotFound();

        $this->actingAs($other)
            ->getJson(route('api.recipes.show', $recipe))
            ->assertNotFound();
    }

    public function test_owner_can_view_own_hidden_recipe(): void
    {
        $owner = User::factory()->create();
        $recipe = Recette::factory()->hidden()->create(['user_id' => $owner->id]);

        $this->actingAs($owner)
            ->getJson(route('api.recipes.show', $recipe))
            ->assertOk()
            ->assertJsonPath('data.statut', 'hidden');
    }

    public function test_admin_can_view_hidden_recipe(): void
    {
        $admin = User::factory()->admin()->create();
        $recipe = Recette::factory()->hidden()->create();

        $this->actingAs($admin)
            ->getJson(route('api.recipes.show', $recipe))
            ->assertOk()
            ->assertJsonPath('data.statut', 'hidden');
    }

    public function test_new_recipe_defaults_to_published(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('api.recipes.store'), ['title' => 'Recette par défaut'])
            ->assertCreated()
            ->assertJsonPath('data.statut', 'published');

        $this->assertDatabaseHas('recettes', [
            'title' => 'Recette par défaut',
            'statut' => 'published',
        ]);
    }

    public function test_owner_can_choose_hidden_or_published_without_admin_action(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('api.recipes.store'), [
                'title' => 'Brouillon',
                'statut' => 'hidden',
            ])
            ->assertCreated()
            ->assertJsonPath('data.statut', 'hidden');

        $recipe = Recette::query()->where('title', 'Brouillon')->firstOrFail();

        $this->actingAs($user)
            ->putJson(route('api.recipes.update', $recipe), [
                'title' => 'Brouillon',
                'statut' => 'published',
            ])
            ->assertOk()
            ->assertJsonPath('data.statut', 'published');
    }
}
