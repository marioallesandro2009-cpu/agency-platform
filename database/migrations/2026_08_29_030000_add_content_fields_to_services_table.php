<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->text('target_audience')->nullable()->after('long_description');
            $table->json('process_steps')->nullable()->after('detail_features');
            $table->json('faqs')->nullable()->after('process_steps');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['target_audience', 'process_steps', 'faqs']);
        });
    }
};
