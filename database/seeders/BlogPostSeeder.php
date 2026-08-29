<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\User;
use Illuminate\Database\Seeder;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::first();
        $tipsCategory = BlogCategory::where("slug", "tips-website")->first();
        $seoCategory = BlogCategory::where("slug", "seo")->first();
        $umkmCategory = BlogCategory::where("slug", "umkm")->first();

        $posts = [
            [
                "title" => "Berapa Biaya Membuat Website untuk Bisnis Kecil?",
                "slug" => "biaya-membuat-website-bisnis-kecil",
                "blog_category_id" => optional($umkmCategory)->id,
                "excerpt" => "Panduan memahami komponen biaya pembuatan website agar bisnis Anda bisa merencanakan anggaran dengan tepat.",
                "content" => "<p>Banyak pemilik bisnis kecil bertanya-tanya berapa sebenarnya biaya yang wajar untuk membuat website. Jawabannya bervariasi tergantung kompleksitas, jumlah halaman, dan fitur yang dibutuhkan.</p><h2>Faktor yang Mempengaruhi Biaya</h2><p>Beberapa faktor utama meliputi jumlah halaman, kebutuhan fitur seperti sistem pemesanan atau katalog produk, serta tingkat kustomisasi desain.</p><h2>Kisaran Harga Umum</h2><p>Untuk landing page sederhana, biaya biasanya lebih terjangkau dibanding website dengan sistem CMS penuh atau toko online.</p>",
                "featured_image" => "/storage/media/070fa5e7-54ab-43a6-bb5f-14e8ed3c4247.jpg", // Pexels, free-commercial-use, photo by alesiakozik (https://www.pexels.com/photo/6771426/)
                "featured_image_alt" => "Kalkulator dan laptop untuk menghitung anggaran biaya pembuatan website",
                "status" => "published",
                "published_at" => now()->subDays(10),
                "focus_keyword" => "biaya membuat website",
                "author_id" => optional($author)->id,
            ],
            [
                "title" => "5 Alasan UMKM Wajib Punya Website di 2026",
                "slug" => "alasan-umkm-wajib-punya-website",
                "blog_category_id" => optional($umkmCategory)->id,
                "excerpt" => "Website bukan lagi pilihan, tapi kebutuhan dasar bagi UMKM yang ingin bertahan dan berkembang.",
                "content" => "<p>Di era digital, kehadiran online menjadi faktor penentu kepercayaan pelanggan terhadap sebuah bisnis, termasuk UMKM.</p><h2>1. Kredibilitas Lebih Tinggi</h2><p>Website memberikan kesan profesional yang sulit didapat hanya dari media sosial.</p><h2>2. Kendali Penuh atas Konten</h2><p>Berbeda dengan platform pihak ketiga, website sepenuhnya milik Anda.</p>",
                "featured_image" => "/storage/media/017a41e7-1f70-4b39-b518-cb95583ca1fe.jpg", // Pexels, free-commercial-use, photo by rdne (https://www.pexels.com/photo/7309930/)
                "featured_image_alt" => "Pemilik UMKM sedang mengelola toko online dari laptop",
                "status" => "published",
                "published_at" => now()->subDays(5),
                "focus_keyword" => "website untuk UMKM",
                "author_id" => optional($author)->id,
            ],
            [
                "title" => "Dasar-Dasar SEO On-Page yang Wajib Diketahui",
                "slug" => "dasar-seo-on-page",
                "blog_category_id" => optional($seoCategory)->id,
                "excerpt" => "Memahami fondasi SEO on-page adalah langkah pertama sebelum website Anda bisa bersaing di halaman pencarian Google.",
                "content" => "<p>SEO on-page mencakup semua elemen di dalam halaman website yang dapat dioptimasi untuk meningkatkan peringkat pencarian.</p><h2>Judul dan Meta Deskripsi</h2><p>Judul yang jelas dan meta deskripsi yang menarik membantu meningkatkan click-through rate dari hasil pencarian.</p><h2>Struktur Heading</h2><p>Penggunaan heading yang terstruktur membantu mesin pencari memahami hierarki konten halaman.</p>",
                "featured_image" => "/storage/media/67b21b3a-fe6e-42d6-8520-abc40ea8b4c3.jpg", // Pexels, free-commercial-use, photo by pavel-danilyuk (https://www.pexels.com/photo/8112204/)
                "featured_image_alt" => "Orang menganalisis data SEO on-page di layar komputer",
                "status" => "published",
                "published_at" => now()->subDays(2),
                "focus_keyword" => "SEO on-page",
                "author_id" => optional($author)->id,
            ],
            [
                "title" => "Draft: Panduan Memilih Domain yang Tepat",
                "slug" => "panduan-memilih-domain",
                "blog_category_id" => optional($tipsCategory)->id,
                "excerpt" => "Artikel ini masih dalam proses penulisan.",
                "content" => "<p>Konten draft, belum dipublikasikan.</p>",
                "featured_image" => null,
                "status" => "draft",
                "published_at" => null,
                "focus_keyword" => "memilih domain",
                "author_id" => optional($author)->id,
            ],
        ];

        foreach ($posts as $post) {
            BlogPost::updateOrCreate(["slug" => $post["slug"]], $post);
        }
    }
}