<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_create_a_category(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson(route('api.categories.store'), [
            'name' => 'Test',
        ])
            ->assertForbidden();

        $this->assertDatabaseMissing('categories', ['name' => 'Test']);
    }

    public function test_non_admin_cannot_update_a_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($user)->putJson(route('api.categories.update', $category), [
            'name' => 'Modifié',
        ])
            ->assertForbidden();

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => $category->name]);
    }

    public function test_non_admin_cannot_delete_a_category(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();

        $this->actingAs($user)->deleteJson(route('api.categories.destroy', $category))
            ->assertForbidden();

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_guest_cannot_create_a_category(): void
    {
        $this->postJson(route('api.categories.store'), [
            'name' => 'Test',
        ])
            ->assertUnauthorized();

        $this->assertDatabaseMissing('categories', ['name' => 'Test']);
    }

    public function test_guest_cannot_update_a_category(): void
    {
        $category = Category::factory()->create();

        $this->putJson(route('api.categories.update', $category), [
            'name' => 'Modifié',
        ])
            ->assertUnauthorized();

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => $category->name]);
    }

    public function test_guest_cannot_delete_a_category(): void
    {
        $category = Category::factory()->create();

        $this->deleteJson(route('api.categories.destroy', $category))
            ->assertUnauthorized();

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
