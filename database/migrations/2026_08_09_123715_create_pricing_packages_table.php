<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pricing_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('description')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('price')->nullable();
            $table->string('price_prefix')->nullable();
            $table->string('price_suffix')->nullable();
            $table->json('features')->nullable();
            $table->string('badge')->nullable();
            $table->boolean('is_recommended')->default(false);
            $table->string('cta_text')->nullable();
            $table->string('cta_url')->nullable();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pricing_packages');
    }
};