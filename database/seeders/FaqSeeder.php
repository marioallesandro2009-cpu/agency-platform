<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            ["question" => "Berapa lama waktu pengerjaan website?", "answer" => "Umumnya 1-3 minggu tergantung kompleksitas fitur dan jumlah halaman. Landing page bisa lebih cepat, sedangkan sistem custom butuh waktu lebih lama.", "category" => "general", "order" => 1],
            ["question" => "Apakah harga sudah termasuk domain dan hosting?", "answer" => "Harga paket fokus pada pengerjaan website. Domain dan hosting bisa dibantu diaturkan terpisah sesuai kebutuhan Anda.", "category" => "pricing", "order" => 2],
            ["question" => "Apakah bisa request revisi desain?", "answer" => "Bisa. Setiap paket sudah termasuk sejumlah revisi selama masa pengerjaan agar hasil sesuai kebutuhan Anda.", "category" => "website", "order" => 3],
            ["question" => "Apakah website yang dibuat sudah SEO friendly?", "answer" => "Ya, kami menerapkan dasar-dasar SEO seperti struktur heading yang benar, meta tag, dan kecepatan loading yang baik.", "category" => "seo", "order" => 4],
            ["question" => "Bagaimana cara memulai konsultasi?", "answer" => "Anda bisa mengisi form konsultasi atau menghubungi kami langsung lewat WhatsApp untuk mendiskusikan kebutuhan website Anda.", "category" => "general", "order" => 5],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(["question" => $faq["question"]], $faq);
        }
    }
}