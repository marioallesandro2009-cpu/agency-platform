<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('portfolio_projects', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('category')
                ->constrained('services')->nullOnDelete();
        });

        // Backfill from the existing free-text category match so projects
        // already linked in practice (category === service name) keep
        // working through the new FK instead of losing the association.
        $services = DB::table('services')->select('id', 'name')->get();
        foreach ($services as $service) {
            DB::table('portfolio_projects')
                ->where('category', $service->name)
                ->whereNull('service_id')
                ->update(['service_id' => $service->id]);
        }
    }

    public function down(): void
    {
        Schema::table('portfolio_projects', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
        });
    }
};
