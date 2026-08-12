<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                "name" => "Contoh Klien A (Demo)",
                "company" => "Toko Contoh",
                "position" => "Pemilik Usaha",
                "testimonial" => "Prosesnya jelas dan komunikasinya enak. Website jadi sesuai yang kami mau, tim juga responsif kalau ada revisi.",
                "rating" => 5,
                "order" => 1,
            ],
            [
                "name" => "Contoh Klien B (Demo)",
                "company" => "Startup Contoh",
                "position" => "Founder",
                "testimonial" => "Kami butuh website cepat buat launching produk, dan tim bisa deliver tepat waktu dengan hasil yang rapi.",
                "rating" => 5,
                "order" => 2,
            ],
            [
                "name" => "Contoh Klien C (Demo)",
                "company" => "Bisnis Contoh",
                "position" => "Marketing Manager",
                "testimonial" => "Setelah website live, traffic organik kami mulai naik pelan-pelan. Support after-launch juga masih aktif dibantu.",
                "rating" => 4,
                "order" => 3,
            ],
        ];

        foreach ($testimonials as $t) {
            Testimonial::updateOrCreate(["name" => $t["name"]], $t);
        }
    }
}