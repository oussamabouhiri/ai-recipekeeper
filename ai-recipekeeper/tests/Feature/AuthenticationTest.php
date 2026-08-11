<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_form_is_displayed(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Join the Kitchen');
    }

    public function test_registration_creates_a_user_with_user_role(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'email' => 'jane@example.com',
            'is_admin' => false,
        ]);

        $this->assertAuthenticated();
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'jane@example.com']);

        $this->post(route('register'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertSame(1, User::where('email', 'jane@example.com')->count());
    }

    public function test_registration_rejects_short_password(): void
    {
        $this->post(route('register'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');

        $this->assertDatabaseMissing('users', ['email' => 'jane@example.com']);
    }

    public function test_registration_never_creates_an_admin(): void
    {
        $this->post(route('register'), [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertSame(0, User::where('email', 'jane@example.com')->where('is_admin', true)->count());
    }

    public function test_login_form_is_displayed(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Welcome back to your kitchen');
    }

    public function test_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'jane@example.com',
            'password' => 'password123',
        ]);

        $this->post(route('login'), [
            'email' => 'jane@example.com',
            'password' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_with_invalid_credentials_fails(): void
    {
        User::factory()->create(['email' => 'jane@example.com']);

        $this->post(route('login'), [
            'email' => 'jane@example.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logout_ends_the_session(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('logout'))
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_login_page_renders_with_tailwind(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('/build/assets/app-')
            ->assertDontSee('bootstrap@5');
    }

    public function test_register_page_renders_with_tailwind(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('/build/assets/app-')
            ->assertDontSee('bootstrap@5');
    }

    public function test_root_guest_redirects_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_root_authenticated_redirects_to_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/')
            ->assertRedirect(route('dashboard'));
    }

    public function test_login_page_has_password_toggle(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('data-toggle-password', false);
    }

    public function test_register_page_has_password_toggles(): void
    {
        $response = $this->get(route('register'))->assertOk();
        $content = $response->getContent();
        $this->assertStringContainsString('data-toggle-password="password"', $content);
        $this->assertStringContainsString('data-toggle-password="password_confirmation"', $content);
    }

    public function test_login_page_shows_auth_background_image(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('images/auth/image.png');
    }

    public function test_register_page_shows_auth_background_image(): void
    {
        $this->get(route('register'))
            ->assertOk()
            ->assertSee('images/auth/image.png');
    }
}
