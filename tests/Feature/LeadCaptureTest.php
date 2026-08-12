<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class LeadCaptureTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // The rate limiter's "array" cache store isn't reset by RefreshDatabase
        // (that only resets the DB), so without this, throttle counts leak
        // across test methods that share the same IP + route key.
        Cache::flush();
    }

    public function test_konsultasi_and_kontak_pages_load(): void
    {
        $this->get('/konsultasi')->assertOk();
        $this->get('/kontak')->assertOk();
    }

    public function test_submitting_the_form_creates_a_lead(): void
    {
        $response = $this->post('/leads', [
            'name' => 'Budi Santoso',
            'whatsapp' => '081234567890',
            'email' => 'budi@example.test',
            'message' => 'Mau tanya soal paket UMKM.',
            'service_interested' => 'Website UMKM',
            'source' => 'konsultasi',
        ]);

        $this->assertDatabaseHas('leads', [
            'name' => 'Budi Santoso',
            'whatsapp' => '081234567890',
            'status' => 'new',
        ]);

        $response->assertStatus(302); // no WA number configured in this test DB, so store() just redirects back()
    }

    public function test_submitting_the_form_redirects_to_whatsapp_when_a_number_is_configured(): void
    {
        Setting::set('whatsapp_number', '6281234567890', 'contact');

        // Inertia::location() only responds 409 (X-Inertia-Location header,
        // for the JS client to catch and do a full navigation) when the
        // request itself carries X-Inertia — otherwise it's just a plain
        // redirect, which is the correct fallback for a non-Inertia caller.
        $response = $this->withHeaders(['X-Inertia' => 'true'])->post('/leads', [
            'name' => 'Budi Santoso',
            'whatsapp' => '081234567890',
            'source' => 'konsultasi',
        ]);

        $response->assertStatus(409);
        $this->assertStringStartsWith('https://wa.me/6281234567890', $response->headers->get('X-Inertia-Location'));
    }

    public function test_invalid_whatsapp_format_is_rejected(): void
    {
        $response = $this->post('/leads', [
            'name' => 'Test',
            'whatsapp' => 'bukan-nomor',
            'source' => 'konsultasi',
        ]);

        $response->assertSessionHasErrors('whatsapp');
        $this->assertDatabaseMissing('leads', ['name' => 'Test']);
    }

    public function test_lead_endpoint_is_rate_limited(): void
    {
        $payload = [
            'name' => 'Rate Limit Test',
            'whatsapp' => '081234567890',
            'source' => 'konsultasi',
        ];

        // Sent with X-Inertia so the friendly-redirect exception handler kicks
        // in, matching how the real frontend form submits.
        for ($i = 0; $i < 5; $i++) {
            $this->withHeaders(['X-Inertia' => 'true'])->post('/leads', $payload);
        }

        $sixthAttempt = $this->withHeaders(['X-Inertia' => 'true'])->post('/leads', $payload);

        $sixthAttempt->assertStatus(302);
        $sixthAttempt->assertSessionHas('error');
        $this->assertSame(5, Lead::count());
    }
}
