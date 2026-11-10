<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\PageSection;
use App\Models\Service;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class SiteTrustTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    public function test_form_submissions_are_never_announced_publicly(): void
    {
        foreach (["PT Satu", "PT Dua", "PT Tiga"] as $name) {
            Lead::create(["name" => $name, "whatsapp" => "081234567890", "service_interested" => "Software Custom", "source" => "konsultasi", "status" => "new"]);
        }

        $this->get("/harga")->assertOk()->assertDontSee("data-live-ticker", false);
    }

    public function test_about_page_does_not_show_a_client_rating(): void
    {
        Testimonial::create(["name" => "Contoh Klien (Demo)", "testimonial" => "Bagus", "rating" => 5, "is_published" => true]);

        $this->get("/tentang-kami")->assertOk()->assertDontSee("Rating Klien");
    }

    public function test_lead_keeps_contact_position_and_visit_origin(): void
    {
        $this->post("/leads", [
            "name" => "PT Maju",
            "whatsapp" => "081234567890",
            "source" => "konsultasi",
            "contact_position" => "Andi, Direktur",
            "utm_source" => "email",
            "utm_medium" => "outreach",
            "utm_campaign" => "developer-perumahan",
            "referrer" => "https://example.test/artikel",
            "landing_page" => "/jasa-software-custom",
        ]);

        $this->assertDatabaseHas("leads", [
            "name" => "PT Maju",
            "contact_position" => "Andi, Direktur",
            "utm_campaign" => "developer-perumahan",
            "landing_page" => "/jasa-software-custom",
        ]);
    }

    public function test_content_migration_replaces_old_copy_and_keeps_admin_edits(): void
    {
        $about = PageSection::create([
            "page" => "about",
            "section_type" => "about_main",
            "content" => [
                "description" => "Agensi untuk bisnis dan UMKM.",
                "mission" => "Misi yang sudah ditulis ulang admin.",
                "vision" => "Mitra UMKM.",
                "values" => [["title" => "Transparansi"]],
                "stats" => [["key" => "blog", "label" => "Artikel"], ["key" => "rating", "label" => "Rating Klien"]],
            ],
        ]);
        $service = Service::create([
            "name" => "Software Custom",
            "slug" => "jasa-software-custom",
            "short_description" => "Dari sistem deteksi dan otomasi AI.",
            "faqs" => [
                ["question" => "Apakah bisa membuat aplikasi Android dan iOS?", "answer" => "Bisa."],
                ["question" => "Siapa pemilik source code?", "answer" => "Anda."],
            ],
        ]);

        (require database_path("migrations/2026_11_10_010000_align_site_content_with_corporate_positioning.php"))->up();

        $content = $about->fresh()->content;
        $this->assertStringNotContainsString("UMKM", $content["description"]);
        $this->assertStringNotContainsString("UMKM", $content["vision"]);
        $this->assertSame("Misi yang sudah ditulis ulang admin.", $content["mission"]);
        $this->assertSame([["title" => "Transparansi"]], $content["values"]);
        $this->assertSame(["blog"], array_column($content["stats"], "key"));

        $service = $service->fresh();
        $this->assertStringNotContainsString("deteksi", $service->short_description);
        $this->assertSame("Apakah sistemnya bisa dibuka dari ponsel?", $service->faqs[0]["question"]);
        $this->assertSame("Siapa pemilik source code?", $service->faqs[1]["question"]);
    }
}
