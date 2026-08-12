<?php

namespace Tests\Feature\Admin;

use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_manage_services(): void
    {
        $this->get('/admin/services')->assertRedirect('/login');
    }

    public function test_admin_can_create_a_service(): void
    {
        $admin = User::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/services', [
            'name' => 'Website Toko Online',
            'short_description' => 'Solusi e-commerce untuk UMKM.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('services', [
            'name' => 'Website Toko Online',
            'slug' => 'website-toko-online',
        ]);
    }

    public function test_admin_can_update_a_service_by_id_even_though_its_route_key_is_slug(): void
    {
        $admin = User::factory()->create();
        $service = Service::create(['name' => 'Company Profile', 'slug' => 'jasa-company-profile', 'order' => 1]);

        // Regression test for the route-model-binding bug fixed in Fase 9: the
        // Service model's getRouteKeyName() is "slug", so admin routes must
        // explicitly bind on {service:id} or this 404s when passed a numeric id.
        $response = $this->actingAs($admin)->put("/admin/services/{$service->id}", [
            'name' => 'Company Profile Updated',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('services', ['id' => $service->id, 'name' => 'Company Profile Updated']);
    }

    public function test_admin_can_delete_a_service_by_id(): void
    {
        $admin = User::factory()->create();
        $service = Service::create(['name' => 'To Delete', 'slug' => 'to-delete', 'order' => 1]);

        $response = $this->actingAs($admin)->delete("/admin/services/{$service->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }

    public function test_is_published_cannot_be_set_directly_through_the_create_form(): void
    {
        $admin = User::factory()->create();

        // is_published defaults to true at the DB level and is deliberately
        // absent from ServiceController::validated() — only togglePublish()
        // may change it. Sending false here should have no effect.
        $this->actingAs($admin)->post('/admin/services', [
            'name' => 'Sneaky Service',
            'is_published' => false,
        ]);

        $service = Service::where('name', 'Sneaky Service')->first();

        $this->assertNotNull($service);
        $this->assertTrue((bool) $service->is_published);
    }
}
