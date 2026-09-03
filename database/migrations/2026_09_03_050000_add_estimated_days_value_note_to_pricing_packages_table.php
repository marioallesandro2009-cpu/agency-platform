<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pricing_packages', function (Blueprint $table) {
            $table->string('estimated_days')->nullable()->after('renewal_price');
            $table->string('value_note')->nullable()->after('estimated_days');
        });
    }

    public function down(): void
    {
        Schema::table('pricing_packages', function (Blueprint $table) {
            $table->dropColumn(['estimated_days', 'value_note']);
        });
    }
};
