<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("services", function (Blueprint $table) {
            $table->boolean("show_portfolio")->default(true)->after("is_published");
            $table->foreignId("blog_category_id")->nullable()->after("show_portfolio")
                ->constrained("blog_categories")->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table("services", function (Blueprint $table) {
            $table->dropForeign(["blog_category_id"]);
            $table->dropColumn(["show_portfolio", "blog_category_id"]);
        });
    }
};