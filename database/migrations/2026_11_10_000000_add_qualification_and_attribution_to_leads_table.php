<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("leads", function (Blueprint $table) {
            $table->string("contact_position")->nullable();
            $table->string("utm_source", 100)->nullable();
            $table->string("utm_medium", 100)->nullable();
            $table->string("utm_campaign", 100)->nullable();
            $table->string("referrer", 500)->nullable();
            $table->string("landing_page", 500)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table("leads", function (Blueprint $table) {
            $table->dropColumn(["contact_position", "utm_source", "utm_medium", "utm_campaign", "referrer", "landing_page"]);
        });
    }
};
