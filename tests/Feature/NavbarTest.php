<?php

namespace Tests\Feature;

use App\Models\NavItem;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavbarTest extends TestCase
{
    use RefreshDatabase;

    /** Only the <header> element: the footer lists services again on purpose. */
    private function header(): string
    {
        $html = $this->get("/")->assertOk()->getContent();
        preg_match("#<header.*?</header>#s", $html, $match);

        return $match[0] ?? "";
    }

    public function test_a_service_with_its_own_menu_link_is_not_repeated_in_the_dropdown(): void
    {
        Service::updateOrCreate(["slug" => "jasa-software-custom"], ["name" => "Software Custom", "is_published" => true, "order" => 1]);
        NavItem::firstOrCreate(["url" => "/jasa-software-custom"], ["label" => "Software Custom", "order" => 2, "is_visible" => true]);

        // One link in the desktop bar and one in the mobile panel: never a third/fourth from the dropdown.
        $this->assertSame(2, substr_count($this->header(), 'href="/jasa-software-custom"'));
    }

    public function test_services_without_a_menu_link_stay_in_the_dropdown(): void
    {
        Service::updateOrCreate(["slug" => "jasa-website-umkm"], ["name" => "Website UMKM", "is_published" => true, "order" => 2]);

        // Dropdown (desktop) + mobile list.
        $this->assertSame(2, substr_count($this->header(), 'href="/jasa-website-umkm"'));
    }

    public function test_the_php_version_is_not_exposed(): void
    {
        $this->get("/")->assertOk()->assertHeaderMissing("X-Powered-By");
    }
}
