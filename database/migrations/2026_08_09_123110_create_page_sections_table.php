<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_sections', function (Blueprint $table) {
            $table->id();
            $table->string('page')->default('home');
            $table->string('section_type');
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->json('content')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();

            $table->unique(['page', 'section_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_sections');
    }
};