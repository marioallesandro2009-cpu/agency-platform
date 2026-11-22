<?php

use App\Models\PortfolioProject;
use App\Models\PricingPackage;
use App\Models\Service;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * The online store offer is not being sold for now: the service page was a 404
     * while its packages were still on the price list. Everything is only unpublished
     * (not deleted), so it can be switched back on from the admin panel.
     */
    public function up(): void
    {
        PricingPackage::where("category", "ecommerce")->update(["is_published" => false]);
        Service::where("slug", "jasa-toko-online")->update(["is_published" => false]);
        PortfolioProject::where("category", "Toko Online")->update(["is_published" => false]);
    }

    public function down(): void
    {
        PricingPackage::where("category", "ecommerce")->update(["is_published" => true]);
        Service::where("slug", "jasa-toko-online")->update(["is_published" => true]);
        PortfolioProject::where("category", "Toko Online")->update(["is_published" => true]);
    }
};
