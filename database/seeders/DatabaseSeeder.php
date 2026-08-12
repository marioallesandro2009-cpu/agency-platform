<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SettingSeeder::class,
            ServiceSeeder::class,
            PageSectionSeeder::class,
            PricingPackageSeeder::class,
            MaintenancePlanSeeder::class,
            PortfolioProjectSeeder::class,
            BlogCategorySeeder::class,
            BlogPostSeeder::class,
            NavItemSeeder::class,
        ]);
    }
}