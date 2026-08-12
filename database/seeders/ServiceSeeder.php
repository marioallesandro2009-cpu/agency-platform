<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                "name" => "Website UMKM",
                "slug" => "jasa-website-umkm",
                "short_description" => "Website sederhana dan terjangkau untuk UMKM yang ingin hadir online.",
                "description" => "Solusi website untuk UMKM yang membutuhkan kehadiran online yang cepat, mudah dikelola, dan terjangkau.",
                "long_description" => "Banyak UMKM masih mengandalkan media sosial sebagai satu-satunya kehadiran online, padahal website memberikan kredibilitas dan kendali penuh atas informasi bisnis. Kami membangun website UMKM yang fokus pada kecepatan, kemudahan pengelolaan, dan konversi pengunjung menjadi pelanggan lewat WhatsApp.",
                "icon" => "store",
                "problems" => [
                    "Pelanggan sulit menemukan info bisnis di luar jam operasional media sosial",
                    "Tidak ada tempat resmi untuk menampilkan produk dan harga",
                    "Sulit bersaing dengan bisnis yang sudah punya website",
                ],
                "features" => ["Desain responsif", "Integrasi WhatsApp", "SEO dasar", "CMS mudah digunakan"],
                "detail_features" => ["Katalog produk/layanan", "Integrasi WhatsApp langsung", "Peta lokasi Google Maps", "Optimasi SEO dasar", "CMS untuk update konten sendiri", "Desain mobile-friendly"],
                "benefits" => ["Meningkatkan kepercayaan pelanggan", "Jangkauan pasar lebih luas"],
                "cta_text" => "Konsultasi Gratis",
                "cta_url" => "/konsultasi",
                "is_featured" => true,
                "order" => 1,
                "is_published" => true,
            ],
            [
                "name" => "Company Profile",
                "slug" => "jasa-website-company-profile",
                "short_description" => "Website profesional untuk membangun kredibilitas perusahaan Anda.",
                "description" => "Website company profile yang menampilkan identitas, layanan, dan portofolio perusahaan secara profesional.",
                "long_description" => "Company profile yang baik menjadi kartu nama digital yang meyakinkan calon klien atau mitra bisnis. Kami membangun struktur halaman yang menonjolkan kredibilitas: profil perusahaan, layanan, proyek yang pernah dikerjakan, dan berita terbaru — semuanya dikelola sendiri lewat CMS tanpa perlu bantuan developer.",
                "icon" => "building",
                "problems" => [
                    "Calon klien ragu karena tidak ada informasi resmi perusahaan secara online",
                    "Sulit menunjukkan portofolio proyek secara terstruktur",
                    "Tidak ada tempat mempublikasikan berita atau pencapaian perusahaan",
                ],
                "features" => ["Desain profesional", "Halaman tim & layanan", "Blog perusahaan", "SEO friendly"],
                "detail_features" => ["Halaman tentang & visi misi", "Halaman tim/struktur organisasi", "Portofolio proyek", "Blog/berita perusahaan", "Formulir kontak", "SEO friendly"],
                "benefits" => ["Kredibilitas bisnis meningkat", "Mudah ditemukan calon klien"],
                "cta_text" => "Konsultasi Gratis",
                "cta_url" => "/konsultasi",
                "is_featured" => true,
                "order" => 2,
                "is_published" => true,
            ],
            [
                "name" => "Toko Online",
                "slug" => "jasa-toko-online",
                "short_description" => "Sistem e-commerce lengkap untuk mulai berjualan online.",
                "description" => "Website toko online dengan sistem katalog produk, keranjang belanja, dan manajemen pesanan.",
                "long_description" => "Berjualan online tidak harus bergantung sepenuhnya pada marketplace pihak ketiga yang membebankan komisi dan membatasi branding. Kami membangun toko online dengan sistem katalog produk, keranjang belanja, checkout, dan dashboard admin untuk mengelola pesanan — memberikan kendali penuh atas bisnis online Anda.",
                "icon" => "shopping-cart",
                "problems" => [
                    "Komisi marketplace mengurangi margin keuntungan",
                    "Branding toko tenggelam di antara kompetitor pada satu platform",
                    "Data pelanggan tidak sepenuhnya menjadi milik bisnis",
                ],
                "features" => ["Katalog produk", "Keranjang & checkout", "Manajemen pesanan", "Payment gateway"],
                "detail_features" => ["Katalog produk dengan varian", "Keranjang belanja", "Checkout terintegrasi", "Payment gateway", "Manajemen pesanan & stok", "Dashboard admin penjualan"],
                "benefits" => ["Penjualan 24 jam", "Jangkauan pasar nasional"],
                "cta_text" => "Konsultasi Gratis",
                "cta_url" => "/konsultasi",
                "is_featured" => true,
                "order" => 3,
                "is_published" => true,
            ],
            [
                "name" => "Custom Web Development",
                "slug" => "jasa-web-development",
                "short_description" => "Aplikasi web custom sesuai kebutuhan bisnis spesifik Anda.",
                "description" => "Pengembangan sistem web custom seperti CRM, sistem booking, dashboard internal, dan integrasi API.",
                "long_description" => "Beberapa kebutuhan bisnis tidak bisa diselesaikan dengan template atau CMS siap pakai. Kami membangun sistem web custom — mulai dari CRM, sistem booking, dashboard internal, hingga integrasi API — yang dirancang mengikuti proses bisnis spesifik Anda, bukan sebaliknya.",
                "icon" => "code",
                "problems" => [
                    "Proses bisnis unik yang tidak bisa diakomodasi software siap pakai",
                    "Kebutuhan integrasi antar sistem/API yang kompleks",
                    "Sistem internal manual yang rawan human error",
                ],
                "features" => ["Analisis kebutuhan", "Sistem custom", "Integrasi API", "Dukungan teknis"],
                "detail_features" => ["Analisis kebutuhan bisnis", "Desain sistem sesuai alur kerja", "Pengembangan aplikasi web custom", "Integrasi API pihak ketiga", "Dashboard & pelaporan", "Dukungan teknis pasca-launching"],
                "benefits" => ["Sesuai proses bisnis", "Skalabel untuk pertumbuhan"],
                "cta_text" => "Konsultasi Gratis",
                "cta_url" => "/konsultasi",
                "is_featured" => true,
                "order" => 4,
                "is_published" => true,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(["slug" => $service["slug"]], $service);
        }
    }
}