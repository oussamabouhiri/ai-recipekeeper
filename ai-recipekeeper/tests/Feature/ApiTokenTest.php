<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_issue_a_token(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->postJson(route('api.tokens.create'), ['name' => 'mobile'])
            ->assertCreated()
            ->assertJsonStructure(['token']);
    }

    public function test_guest_cannot_issue_a_token(): void
    {
        $this->postJson(route('api.tokens.create'), ['name' => 'mobile'])
            ->assertUnauthorized();
    }

    public function test_valid_token_accesses_protected_route(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->getJson(route('api.user'))
            ->assertOk()
            ->assertJson(['id' => $user->id]);
    }

    public function test_invalid_token_is_rejected(): void
    {
        $this->withToken('invalid-token')
            ->getJson(route('api.user'))
            ->assertUnauthorized();
    }
}
