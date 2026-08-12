<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pricing_packages', function (Blueprint $table) {
            $table->string('category')->nullable()->after('slug');
            $table->string('tier')->nullable()->after('category');
            $table->unsignedBigInteger('renewal_price')->nullable()->after('price_suffix');
            $table->unique(['category', 'tier']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pricing_packages', function (Blueprint $table) {
            $table->dropUnique(['category', 'tier']);
            $table->dropColumn(['category', 'tier', 'renewal_price']);
        });
    }
};
