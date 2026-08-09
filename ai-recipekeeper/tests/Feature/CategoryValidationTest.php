<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_missing_name_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson(route('api.categories.store'), [
            'description' => 'Without name',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_duplicate_name_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        Category::factory()->create(['name' => 'Plat principal']);

        $this->actingAs($admin)->postJson(route('api.categories.store'), [
            'name' => 'Plat principal',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_name_too_long_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson(route('api.categories.store'), [
            'name' => str_repeat('a', 256),
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_updating_with_duplicate_name_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Original']);
        Category::factory()->create(['name' => 'Existant']);

        $this->actingAs($admin)->putJson(route('api.categories.update', $category), [
            'name' => 'Existant',
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');
    }

    public function test_updating_with_same_name_is_allowed(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Plat principal']);

        $this->actingAs($admin)->putJson(route('api.categories.update', $category), [
            'name' => 'Plat principal',
            'description' => 'Updated description',
        ])
            ->assertOk()
            ->assertJsonPath('data.name', 'Plat principal');
    }
}
