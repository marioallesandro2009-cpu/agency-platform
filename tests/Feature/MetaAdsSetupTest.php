<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class MetaAdsSetupTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_landing_and_privacy_pages_load(): void
    {
        $this->get("/software-developer-perumahan")->assertOk()->assertSee("developer-cta-form", false);
        $this->get("/kebijakan-privasi")->assertOk();
    }

    public function test_landing_page_is_in_the_sitemap(): void
    {
        $this->get("/sitemap.xml")->assertOk()->assertSee("/software-developer-perumahan", false);
    }

    public function test_pixel_setting_row_exists_for_the_admin_form(): void
    {
        $this->assertDatabaseHas("settings", ["key" => "meta_pixel_id", "group" => "integrations"]);
    }

    public function test_pixel_is_absent_until_an_id_is_set(): void
    {
        $this->get("/software-developer-perumahan")->assertDontSee("fbevents.js", false);
    }

    public function test_pixel_loads_with_a_valid_id_and_csp_allows_it(): void
    {
        Setting::set("meta_pixel_id", "123456789012345", "integrations");

        $response = $this->get("/software-developer-perumahan");

        $response->assertSee("fbevents.js", false)->assertSee("fbq('init', '123456789012345')", false);
        $this->assertStringContainsString("https://connect.facebook.net", $response->headers->get("Content-Security-Policy"));
        $this->assertStringContainsString("https://www.facebook.com", $response->headers->get("Content-Security-Policy"));
    }

    public function test_a_non_numeric_pixel_id_is_ignored(): void
    {
        Setting::set("meta_pixel_id", "12345'); alert('x", "integrations");

        $this->get("/software-developer-perumahan")->assertDontSee("fbevents.js", false);
    }
}
