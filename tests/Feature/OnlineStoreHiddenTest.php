<?php

namespace Tests\Feature;

use App\Models\PortfolioProject;
use App\Models\PricingPackage;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OnlineStoreHiddenTest extends TestCase
{
    use RefreshDatabase;

    private function package(string $category, bool $published = true): PricingPackage
    {
        return PricingPackage::create([
            "name" => "{$category} paket", "slug" => "{$category}-seed", "category" => $category, "tier" => "seed",
            "price" => 1500000, "features" => [["label" => "Fitur"]], "order" => 1, "is_published" => $published,
        ]);
    }

    private function runMigration(): void
    {
        (require database_path("migrations/2026_11_22_020000_hide_online_store_offer.php"))->up();
    }

    public function test_migration_unpublishes_the_online_store_offer_everywhere(): void
    {
        $package = $this->package("ecommerce");
        $other = $this->package("company_profile");
        $service = Service::create(["name" => "Toko Online", "slug" => "jasa-toko-online", "is_published" => true, "order" => 1]);
        $demo = PortfolioProject::create(["name" => "Toko Demo", "slug" => "toko-demo", "category" => "Toko Online", "is_published" => true, "order" => 9]);

        $this->runMigration();
        $this->runMigration();

        $this->assertFalse($package->fresh()->is_published);
        $this->assertFalse($service->fresh()->is_published);
        $this->assertFalse($demo->fresh()->is_published);
        $this->assertTrue($other->fresh()->is_published, "Other packages must stay published.");
    }

    public function test_ecommerce_portfolio_demo_is_hidden_but_other_projects_stay(): void
    {
        $shop = PortfolioProject::create(["name" => "ExoGadget", "slug" => "exogadget", "category" => "Website E-commerce", "is_published" => true, "order" => 9]);
        $other = PortfolioProject::create(["name" => "AutoCraft", "slug" => "autocraft", "category" => "Company Profile / Bengkel", "is_published" => true, "order" => 10]);

        (require database_path("migrations/2026_11_22_030000_hide_ecommerce_portfolio_demo.php"))->up();

        $this->assertFalse($shop->fresh()->is_published);
        $this->assertTrue($other->fresh()->is_published);
        $this->get("/portfolio")->assertOk()->assertDontSee("ExoGadget")->assertSee("AutoCraft");
    }

    public function test_pricing_page_has_no_ecommerce_tab_without_a_published_package(): void
    {
        $this->package("ecommerce", published: false);
        $this->package("company_profile");

        $this->get("/harga")->assertOk()
            ->assertSee('data-tab-trigger="company_profile"', false)
            ->assertDontSee('data-tab-trigger="ecommerce"', false)
            ->assertDontSee("E-commerce");
    }

    public function test_ecommerce_tab_returns_once_a_package_is_published_again(): void
    {
        $this->package("ecommerce");
        $this->package("company_profile");

        $this->get("/harga")->assertOk()->assertSee('data-tab-trigger="ecommerce"', false);
    }

    public function test_online_store_service_page_is_not_available_when_unpublished(): void
    {
        Service::create(["name" => "Toko Online", "slug" => "jasa-toko-online", "is_published" => false, "order" => 1]);

        $this->get("/jasa-toko-online")->assertNotFound();
        $this->get("/sitemap.xml")->assertOk()->assertDontSee("jasa-toko-online", false);
    }

    public function test_home_price_list_skips_the_hidden_category(): void
    {
        $this->package("ecommerce", published: false);
        $this->package("company_profile");

        $this->get("/")->assertOk()->assertDontSee("E-commerce");
    }
}
