<?php

namespace Tests\Feature;

use App\Models\Recette;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RecipeAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_own_recipe(): void
    {
        $owner = User::factory()->create();
        $recipe = Recette::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($owner)
            ->putJson(route('api.recipes.update', $recipe), ['title' => 'Mis à jour'])
            ->assertOk()
            ->assertJsonPath('data.title', 'Mis à jour');
    }

    public function test_admin_can_update_any_recipe(): void
    {
        $admin = User::factory()->admin()->create();
        $recipe = Recette::factory()->create();

        $this->actingAs($admin)
            ->putJson(route('api.recipes.update', $recipe), ['title' => 'Modéré'])
            ->assertOk();
    }

    public function test_non_owner_cannot_update_a_recipe(): void
    {
        $other = User::factory()->create();
        $recipe = Recette::factory()->create();

        $this->actingAs($other)
            ->putJson(route('api.recipes.update', $recipe), ['title' => 'Volé'])
            ->assertForbidden();
    }

    public function test_owner_can_delete_own_recipe(): void
    {
        $owner = User::factory()->create();
        $recipe = Recette::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($owner)
            ->deleteJson(route('api.recipes.destroy', $recipe))
            ->assertNoContent();

        $this->assertDatabaseMissing('recettes', ['id' => $recipe->id]);
    }

    public function test_admin_can_delete_any_recipe(): void
    {
        $admin = User::factory()->admin()->create();
        $recipe = Recette::factory()->create();

        $this->actingAs($admin)
            ->deleteJson(route('api.recipes.destroy', $recipe))
            ->assertNoContent();

        $this->assertDatabaseMissing('recettes', ['id' => $recipe->id]);
    }

    public function test_non_owner_cannot_delete_a_recipe(): void
    {
        $other = User::factory()->create();
        $recipe = Recette::factory()->create();

        $this->actingAs($other)
            ->deleteJson(route('api.recipes.destroy', $recipe))
            ->assertForbidden();

        $this->assertDatabaseHas('recettes', ['id' => $recipe->id]);
    }
}
