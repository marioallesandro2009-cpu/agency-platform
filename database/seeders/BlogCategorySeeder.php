<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use Illuminate\Database\Seeder;

class BlogCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ["name" => "Tips Website", "slug" => "tips-website", "description" => "Tips dan panduan seputar website untuk bisnis."],
            ["name" => "SEO", "slug" => "seo", "description" => "Artikel seputar optimasi mesin pencari."],
            ["name" => "UMKM", "slug" => "umkm", "description" => "Konten khusus untuk pelaku UMKM."],
            ["name" => "Studi Kasus", "slug" => "studi-kasus", "description" => "Pembahasan proyek dan studi kasus."],
        ];

        foreach ($categories as $category) {
            BlogCategory::updateOrCreate(["slug" => $category["slug"]], $category);
        }
    }
}