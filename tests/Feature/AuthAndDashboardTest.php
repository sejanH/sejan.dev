<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class AuthAndDashboardTest extends TestCase
{
    use DatabaseTransactions;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('sejan');
        $response->assertSee('Registration Disabled');
    }

    public function test_register_route_is_disabled(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(403);
        $response->assertSee('Public registration is disabled');
    }

    public function test_guest_cannot_access_admin_panel(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/login');
    }

    public function test_seeded_admin_can_authenticate_and_access_admin_panel(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->post('/login', [
            'email' => 'admin@sejan.dev',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/admin');

        $adminResponse = $this->get('/admin');
        $adminResponse->assertStatus(200);
        $adminResponse->assertSeeText('Analytics & Overview', false);
        $adminResponse->assertSee('admin@sejan.dev');
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $this->seed(DatabaseSeeder::class);

        $response = $this->post('/login', [
            'email' => 'admin@sejan.dev',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_non_admin_user_cannot_access_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'normal@example.com',
            'password' => bcrypt('password'),
            'is_admin' => false,
            'role' => 'user',
        ]);

        $response = $this->post('/login', [
            'email' => 'normal@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_authenticated_admin_can_logout(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->post('/login', [
            'email' => 'admin@sejan.dev',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }
}
