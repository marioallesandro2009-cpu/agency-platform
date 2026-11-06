<?php

namespace Tests\Feature;

use App\Models\PageVisit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerformanceMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_visit_is_recorded_for_a_public_page(): void
    {
        $this->get('/blog')->assertOk();

        $this->assertSame(1, PageVisit::where('path', 'blog')->count());
    }

    public function test_post_requests_are_not_recorded_as_visits(): void
    {
        $this->post('/leads', ['name' => 'Budi', 'whatsapp' => '081234567890', 'source' => 'kontak']);

        $this->assertSame(0, PageVisit::count());
    }

    public function test_server_timing_header_is_sent(): void
    {
        $this->get('/blog')
            ->assertOk()
            ->assertHeader('Server-Timing');

        $this->assertMatchesRegularExpression('/^app;dur=\d+(\.\d+)?$/', $this->get('/faq')->headers->get('Server-Timing'));
    }
}
