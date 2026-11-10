<?php

namespace Database\Seeders;

use App\Models\PageSection;
use Illuminate\Database\Seeder;

class PageSectionSeeder extends Seeder
{
    public function run(): void
    {
        $sections = [
            [
                "page" => "home",
                "section_type" => "hero",
                "title" => "Jasa Website Profesional untuk Bisnis & UMKM",
                "subtitle" => "Kami bantu bisnis Anda hadir online dengan website cepat, modern, dan SEO-friendly.",
                "content" => [
                    "eyebrow" => "Jasa Website Profesional",
                    "cta_primary_text" => "Konsultasi Gratis",
                    "cta_primary_url" => "/konsultasi",
                    "cta_secondary_text" => "Lihat Portfolio",
                    "cta_secondary_url" => "/portfolio",
                ],
                "is_visible" => true,
                "order" => 1,
            ],
            [
                "page" => "home",
                "section_type" => "about",
                "title" => "Kenalan Lebih Dekat dengan Kami",
                "subtitle" => null,
                "content" => [
                    "description" => "Kami membangun website dan software custom untuk perusahaan di Indonesia. Setiap proyek dimulai dari memahami cara kerja tim Anda, supaya hasilnya benar-benar dipakai dan mendukung tujuan bisnis.",
                    "button_text" => "Lihat Selengkapnya",
                    "button_url" => "/tentang-kami",
                ],
                "is_visible" => true,
                "order" => 2,
            ],
            [
                "page" => "home",
                "section_type" => "services",
                "title" => "Solusi Website untuk Setiap Kebutuhan",
                "subtitle" => "Layanan Kami",
                "content" => null,
                "is_visible" => true,
                "order" => 3,
            ],
            [
                "page" => "home",
                "section_type" => "portfolio",
                "title" => "Hasil Karya Kami",
                "subtitle" => "Portfolio",
                "content" => null,
                "is_visible" => true,
                "order" => 4,
            ],
            [
                "page" => "home",
                "section_type" => "why_us",
                "title" => "Kenapa Memilih Kami",
                "subtitle" => "Keunggulan",
                "content" => [
                    "items" => [
                        ["title" => "Desain Custom", "description" => "Bukan template pasaran, setiap website dirancang sesuai identitas bisnis Anda."],
                        ["title" => "SEO Friendly", "description" => "Dibangun dengan fondasi SEO yang baik agar mudah ditemukan di Google."],
                        ["title" => "Support Berkelanjutan", "description" => "Kami dampingi setelah website live, bukan cuma serah terima."],
                        ["title" => "Harga Transparan", "description" => "Tidak ada biaya tersembunyi, semua dijelaskan di awal."],
                        ["title" => "Kode Custom, Bukan Template", "description" => "Dibangun dari kode asli tanpa tumpukan plugin pihak ketiga, sehingga performa lebih cepat, celah keamanan lebih sedikit, dan fitur bisa disesuaikan penuh dengan kebutuhan bisnis Anda.", "icon" => "code"],
                    ],
                ],
                "is_visible" => true,
                "order" => 4,
            ],
            [
                "page" => "home",
                "section_type" => "process",
                "title" => "Proses Kerja Kami",
                "subtitle" => "Bagaimana Kami Bekerja",
                "content" => [
                    "steps" => [
                        ["title" => "Konsultasi", "description" => "Diskusi kebutuhan dan tujuan website Anda."],
                        ["title" => "Perencanaan", "description" => "Menyusun struktur, desain, dan fitur website."],
                        ["title" => "Pengembangan", "description" => "Proses desain dan development website."],
                        ["title" => "Peluncuran", "description" => "Website live dan siap digunakan."],
                    ],
                ],
                "is_visible" => true,
                "order" => 5,
            ],
            [
                "page" => "home",
                "section_type" => "pricing",
                "title" => "Paket Harga",
                "subtitle" => "Investasi",
                "content" => null,
                "is_visible" => true,
                "order" => 6,
            ],
            [
                "page" => "home",
                "section_type" => "testimonials",
                "title" => "Apa Kata Klien Kami",
                "subtitle" => "Testimoni",
                "content" => null,
                "is_visible" => true,
                "order" => 7,
            ],
            [
                "page" => "home",
                "section_type" => "faq",
                "title" => "Pertanyaan yang Sering Diajukan",
                "subtitle" => "FAQ",
                "content" => null,
                "is_visible" => true,
                "order" => 8,
            ],
            [
                "page" => "home",
                "section_type" => "cta",
                "title" => "Siap Membangun Website Impian Anda?",
                "subtitle" => "Mulai Sekarang",
                "content" => [
                    "description" => "Konsultasikan kebutuhan website Anda secara gratis, tanpa komitmen.",
                    "cta_text" => "Konsultasi Gratis Sekarang",
                    "cta_url" => "/konsultasi",
                ],
                "is_visible" => true,
                "order" => 9,
            ],
            [
                "page" => "about",
                "section_type" => "about_main",
                "title" => "Tentang Kami",
                "subtitle" => "Siapa Kami",
                "content" => [
                    "description" => "Exodine Web adalah studio pengembangan website dan software custom untuk perusahaan di Indonesia. Kami membantu tim merapikan pekerjaan yang masih bergantung pada spreadsheet, chat, dan proses manual, mulai dari website perusahaan sampai sistem operasional yang mengikuti alur kerja Anda.",
                    "mission" => "Membangun website dan software yang mudah dipakai, tepat sasaran, dan bisa dikembangkan bertahap sesuai kebutuhan tiap perusahaan.",
                    "mission_icon" => "target",
                    "vision" => "Menjadi mitra teknologi terpercaya bagi perusahaan di Indonesia yang ingin bekerja lebih rapi dan terukur.",
                    "vision_icon" => "compass",
                    "values" => [
                        ["title" => "Transparansi", "description" => "Harga dan proses kerja yang jelas sejak awal, tanpa biaya tersembunyi.", "icon" => "shield-check"],
                        ["title" => "Kualitas", "description" => "Setiap website dibangun dengan standar kode dan desain yang rapi.", "icon" => "check-circle"],
                        ["title" => "Kolaboratif", "description" => "Kami mendengarkan kebutuhan klien sebagai mitra, bukan sekadar vendor.", "icon" => "users"],
                        ["title" => "Tepat Waktu", "description" => "Kami menyelesaikan proyek sesuai linimasa yang disepakati sejak awal.", "icon" => "clock"],
                    ],
                    "technologies" => ["Laravel", "Vue.js", "Inertia.js", "Tailwind CSS", "MySQL"],
                ],
                "is_visible" => true,
                "order" => 1,
            ],
        ];

        foreach ($sections as $section) {
            PageSection::updateOrCreate(
                ["page" => $section["page"], "section_type" => $section["section_type"]],
                $section
            );
        }
    }
}