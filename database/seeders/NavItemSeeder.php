<?php

namespace Database\Seeders;

use App\Models\NavItem;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NavItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $items = [
            ["label" => "Home", "url" => "/", "order" => 1],
            ["label" => "Harga", "url" => "/harga", "order" => 2],
            ["label" => "Portfolio", "url" => "/portfolio", "order" => 3],
            ["label" => "Blog", "url" => "/blog", "order" => 4],
            ["label" => "Tentang Kami", "url" => "/tentang-kami", "order" => 5],
            ["label" => "Kontak", "url" => "/kontak", "order" => 6],
        ];

        foreach ($items as $item) {
            NavItem::updateOrCreate(["label" => $item["label"], "url" => $item["url"]], $item);
        }
    }
}
