<?php

namespace Database\Seeders;

use App\Models\PortfolioProject;
use Illuminate\Database\Seeder;

class PortfolioProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                "name" => "Website UMKM Kopi Nusantara (Konsep)",
                "slug" => "kopi-nusantara-konsep",
                "category" => "Website UMKM",
                "short_description" => "Konsep website untuk UMKM kedai kopi dengan katalog menu dan integrasi WhatsApp.",
                "description" => "Proyek konsep untuk menunjukkan bagaimana kedai kopi skala kecil dapat memiliki kehadiran online yang profesional dengan budget terjangkau.",
                "challenge" => "UMKM membutuhkan cara mudah menampilkan menu dan lokasi tanpa biaya tinggi.",
                "solution" => "Dibangun website satu halaman dengan katalog menu, integrasi Google Maps, dan tombol WhatsApp langsung.",
                "features" => ["Katalog menu", "Integrasi WhatsApp", "Google Maps", "Responsive"],
                "technologies" => ["Laravel", "Vue", "Tailwind CSS"],
                "client_label" => "Proyek Konsep / Demo",
                "project_type" => "personal",
                "thumbnail" => "/storage/media/f92b4cd7-d852-4fc7-8488-54c5a73b5ef6.jpg", // Pexels, free-commercial-use, photo by enisahalidi (https://www.pexels.com/photo/29516134/)
                "cover_image" => "/storage/media/f92b4cd7-d852-4fc7-8488-54c5a73b5ef6.jpg",
                "gallery" => [],
                "live_url" => null,
                "is_featured" => true,
                "is_published" => true,
                "order" => 1,
            ],
            [
                "name" => "Company Profile Manufaktur ABC (Konsep)",
                "slug" => "manufaktur-abc-konsep",
                "category" => "Company Profile",
                "short_description" => "Konsep website company profile untuk perusahaan manufaktur dengan halaman produk dan berita.",
                "description" => "Proyek konsep company profile yang menekankan kredibilitas perusahaan melalui halaman tentang, layanan, dan portofolio proyek.",
                "challenge" => "Perusahaan membutuhkan citra profesional untuk menarik klien korporat.",
                "solution" => "Dibangun struktur halaman lengkap: tentang, layanan, proyek, dan blog berita perusahaan.",
                "features" => ["Halaman tim", "Portofolio proyek", "Blog perusahaan", "SEO friendly"],
                "technologies" => ["Laravel", "Inertia.js", "Vue", "Tailwind CSS"],
                "client_label" => "Proyek Konsep / Demo",
                "project_type" => "personal",
                "thumbnail" => "/storage/media/ec4448b2-e4c2-49a9-b093-2ba2e75a583b.jpg", // Pexels, free-commercial-use, photo by mazhar-ulazhar (https://www.pexels.com/photo/31352672/)
                "cover_image" => "/storage/media/ec4448b2-e4c2-49a9-b093-2ba2e75a583b.jpg",
                "gallery" => [],
                "live_url" => null,
                "is_featured" => true,
                "is_published" => true,
                "order" => 2,
            ],
            [
                "name" => "Toko Online Fashionku (Konsep)",
                "slug" => "fashionku-konsep",
                "category" => "Toko Online",
                "short_description" => "Konsep toko online untuk brand fashion lokal dengan sistem katalog dan checkout.",
                "description" => "Proyek konsep e-commerce yang menunjukkan alur belanja lengkap dari katalog produk hingga checkout.",
                "challenge" => "Brand fashion lokal ingin mulai berjualan online tanpa bergantung pada marketplace pihak ketiga.",
                "solution" => "Dibangun sistem katalog produk dengan varian, keranjang belanja, dan proses checkout sederhana.",
                "features" => ["Katalog produk", "Varian produk", "Keranjang belanja", "Checkout"],
                "technologies" => ["Laravel", "Vue", "Tailwind CSS"],
                "client_label" => "Proyek Konsep / Demo",
                "project_type" => "personal",
                "thumbnail" => "/storage/media/eae148c6-3ed8-40b2-b437-74bd9c9a7dca.jpg", // Pexels, free-commercial-use, photo by delot (https://www.pexels.com/photo/19599222/)
                "cover_image" => "/storage/media/eae148c6-3ed8-40b2-b437-74bd9c9a7dca.jpg",
                "gallery" => [],
                "live_url" => null,
                "is_featured" => true,
                "is_published" => true,
                "order" => 3,
            ],
            [
                "name" => "Sistem Booking Klinik Sehat (Konsep)",
                "slug" => "klinik-sehat-konsep",
                "category" => "Custom Web Development",
                "short_description" => "Konsep sistem booking online untuk klinik kesehatan dengan jadwal dokter.",
                "description" => "Proyek konsep aplikasi web custom untuk manajemen jadwal dokter dan booking pasien secara online.",
                "challenge" => "Klinik membutuhkan sistem antrian online untuk mengurangi penumpukan pasien di lokasi.",
                "solution" => "Dibangun sistem booking dengan pemilihan dokter, jadwal, dan konfirmasi otomatis.",
                "features" => ["Jadwal dokter", "Booking online", "Dashboard admin", "Notifikasi"],
                "technologies" => ["Laravel", "Vue", "MySQL"],
                "client_label" => "Proyek Konsep / Demo",
                "project_type" => "personal",
                "thumbnail" => "/storage/media/e8c8086d-7cf8-40f1-9bf3-11fec49d0e80.jpg", // Pexels, free-commercial-use, photo by cottonbro (https://www.pexels.com/photo/7579831/)
                "cover_image" => "/storage/media/e8c8086d-7cf8-40f1-9bf3-11fec49d0e80.jpg",
                "gallery" => [],
                "live_url" => null,
                "is_featured" => false,
                "is_published" => true,
                "order" => 4,
            ],
        ];

        foreach ($projects as $project) {
            PortfolioProject::updateOrCreate(["slug" => $project["slug"]], $project);
        }
    }
}