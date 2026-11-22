<?php

use App\Models\PortfolioProject;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Follow-up to hide_online_store_offer: the live demo carries the category
     * "Website E-commerce", not "Toko Online", so it was missed. Unpublish only (not deleted).
     */
    public function up(): void
    {
        PortfolioProject::where("category", "like", "%commerce%")->update(["is_published" => false]);
    }

    public function down(): void
    {
        PortfolioProject::where("category", "like", "%commerce%")->update(["is_published" => true]);
    }
};
