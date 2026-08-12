<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create("page_visits", function (Blueprint $table) {
            $table->id();
            $table->string("path");
            $table->string("ip_address", 45)->nullable();
            $table->string("session_id")->nullable();
            $table->string("user_agent")->nullable();
            $table->string("referrer")->nullable();
            $table->date("visit_date")->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists("page_visits");
    }
};