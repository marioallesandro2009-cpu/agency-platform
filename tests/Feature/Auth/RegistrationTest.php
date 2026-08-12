<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_requires_an_authenticated_admin(): void
    {
        $response = $this->get('/register');

        $response->assertRedirect('/login');
    }

    public function test_registration_screen_can_be_rendered_by_an_admin(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($admin)->get('/register');

        $response->assertStatus(200);
    }

    public function test_an_admin_can_register_a_new_staff_user(): void
    {
        $admin = User::factory()->create(['role' => 'super_admin']);

        $response = $this->actingAs($admin)->post('/register', [
            'name' => 'New Staff',
            'email' => 'staff@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'staff@example.com',
            'role' => 'editor',
        ]);

        // Creating a new user must not hijack the admin's own session.
        $this->assertAuthenticatedAs($admin);
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_a_guest_cannot_register_themselves(): void
    {
        $response = $this->post('/register', [
            'name' => 'Attacker',
            'email' => 'attacker@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'attacker@example.com']);
    }
}
