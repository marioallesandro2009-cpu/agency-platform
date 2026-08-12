<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect('/login');
    }

    public function test_authenticated_editor_can_access_the_admin_panel(): void
    {
        $user = User::factory()->create(['role' => 'editor']);

        $this->actingAs($user)->get('/admin')->assertOk();
    }

    public function test_a_user_with_an_unrecognized_role_is_forbidden(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $this->actingAs($user)->get('/admin')->assertForbidden();
    }

    public function test_login_redirects_straight_to_the_admin_panel(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }
}
