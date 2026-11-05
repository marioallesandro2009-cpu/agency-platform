<?php

namespace Database\Seeders;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\NavItem;
use App\Models\PageSection;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

/**
 * Positioning "Website & Software Custom" untuk klien korporat: layanan
 * Software Custom, 2 kategori blog, 12 artikel SEO, menu, dan copy beranda.
 * Aman dijalankan berulang dan bisa dijalankan sendiri di production:
 *   php artisan db:seed --class=CorporateContentSeeder
 */
class CorporateContentSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(BlogCategorySeeder::class);
        $categories = $this->seedCategories();
        $this->seedService($categories["custom-software"]);
        $this->seedPosts($categories);
        $this->seedNavigation();
        $this->seedPositioning();
    }

    private function seedCategories(): array
    {
        $rows = [
            ["name" => "Custom Software", "slug" => "custom-software", "description" => "Panduan software custom dan pembuatan aplikasi untuk perusahaan."],
            ["name" => "Website Korporat", "slug" => "website-korporat", "description" => "Strategi website untuk perusahaan dan klien B2B."],
        ];

        foreach ($rows as $row) {
            BlogCategory::updateOrCreate(["slug" => $row["slug"]], $row);
        }

        // Kosong = layanan tidak menampilkan artikel terkait. Yang sudah diisi
        // admin tidak ditimpa.
        $corporate = BlogCategory::where("slug", "website-korporat")->first();
        Service::whereIn("slug", ["jasa-website-company-profile", "jasa-web-development"])
            ->whereNull("blog_category_id")
            ->update(["blog_category_id" => $corporate->id]);

        return BlogCategory::pluck("id", "slug")->all();
    }

    private function seedService(int $categoryId): void
    {
        Service::updateOrCreate(["slug" => "jasa-software-custom"], [
            "name" => "Software Custom",
            "short_description" => "Software dan aplikasi yang dibangun mengikuti cara kerja bisnis Anda, bukan sebaliknya.",
            "badge_label" => "Untuk Perusahaan",
            "description" => "Sistem internal, integrasi, dan aplikasi mobile yang dirancang sesuai proses bisnis perusahaan Anda.",
            "icon" => "code",
            "image" => "/images/blog/jasa-software-custom.jpg",
            "seo_title" => "Jasa Pembuatan Software Custom dan Aplikasi Perusahaan",
            "seo_description" => "Jasa pembuatan software custom dan aplikasi yang mengikuti proses bisnis perusahaan Anda. Estimasi tertulis per modul, dibangun bertahap, biaya terkendali.",
            "features" => [
                "ERP, HRIS, CRM, inventory, dan portal klien sesuai alur kerja Anda",
                "Pembuatan aplikasi web dan mobile dalam satu tim",
                "Estimasi tertulis per modul setelah kebutuhan dipetakan",
            ],
            "faqs" => [
                ["question" => "Kenapa tidak ada harga pasti di halaman ini?", "answer" => "Karena biaya ditentukan oleh proses, integrasi, dan platform yang dibutuhkan. Angka yang diberikan sebelum memahami hal itu hanya tebakan. Setelah diskusi kebutuhan, Anda menerima estimasi tertulis per modul lengkap dengan asumsi dan hal yang tidak termasuk."],
                ["question" => "Apakah konsultasi awal dikenakan biaya?", "answer" => "Tidak. Diskusi awal gratis dan tanpa kewajiban untuk melanjutkan. Dari sesi ini, Anda sudah mendapat gambaran apakah software custom memang tepat untuk masalah Anda, atau cukup memakai aplikasi yang sudah ada."],
                ["question" => "Bagaimana jika anggaran kami terbatas?", "answer" => "Kami bisa memulai dari modul yang paling berdampak, lalu mengembangkannya bertahap. Anda tidak perlu membangun semuanya sekaligus, dan keputusan tahap berikutnya didasarkan pada pemakaian nyata."],
                ["question" => "Apakah bisa membuat aplikasi Android dan iOS?", "answer" => "Bisa. Pembuatan aplikasi termasuk dalam layanan ini. Kami akan merekomendasikan aplikasi web, PWA, atau mobile sesuai pengguna dan anggaran, sehingga Anda tidak membayar platform yang tidak dibutuhkan."],
                ["question" => "Siapa pemilik source code setelah proyek selesai?", "answer" => "Kepemilikan source code, dokumentasi, dan akses kami tuliskan dengan jelas di kontrak, sehingga sistem tetap bisa dikembangkan oleh tim Anda atau pihak lain di kemudian hari."],
                ["question" => "Apakah data perusahaan kami aman selama proyek?", "answer" => "Kami bersedia menandatangani NDA sebelum diskusi detail. Hak akses dibatasi sesuai peran, dan praktik keamanan menjadi bagian dari rancangan sistem sejak awal."],
                ["question" => "Apakah sistem bisa terhubung dengan aplikasi yang sudah kami pakai?", "answer" => "Umumnya bisa, jika aplikasi tersebut menyediakan API atau ekspor data. Kelayakannya kami periksa pada tahap pemetaan, sebelum Anda memutuskan untuk melanjutkan."],
            ],
            "cta_text" => "Diskusikan Kebutuhan",
            "cta_url" => "/konsultasi?service=Software%20Custom",
            "is_featured" => true,
            "order" => 5,
            "is_published" => true,
            "show_portfolio" => false,
            "blog_category_id" => $categoryId,
        ]);
    }

    private function seedPosts(array $categories): void
    {
        $authorId = optional(User::first())->id;
        $posts = array_merge(
            require __DIR__ . "/data/blog-website-posts.php",
            require __DIR__ . "/data/blog-software-posts.php",
        );

        foreach ($posts as $post) {
            $graphic = '<img src="/images/blog/' . $post["slug"] . '-infografik.webp" alt="' . e($post["graphic_alt"]) . '" width="1200" height="675" loading="lazy">';

            BlogPost::updateOrCreate(["slug" => $post["slug"]], [
                "title" => $post["title"],
                "seo_title" => $post["seo_title"],
                "seo_description" => $post["seo_description"],
                "focus_keyword" => $post["focus_keyword"],
                "excerpt" => $post["excerpt"],
                // Template blog memakai whitespace-pre-line: baris baru antar-tag
                // akan tampil sebagai jarak kosong, jadi dibuang di sini.
                "content" => str_replace("[[infografik]]", $graphic, preg_replace("/\s*\n\s*/", "", $post["content"])),
                "featured_image" => "/images/blog/" . $post["image"] . ".jpg",
                "featured_image_alt" => $post["image_alt"],
                "blog_category_id" => $categories[$post["category"]],
                "author_id" => $authorId,
                "status" => "published",
                "published_at" => Carbon::parse($post["published_at"]),
            ]);
        }
    }

    private function seedNavigation(): void
    {
        $item = NavItem::firstOrCreate(
            ["url" => "/jasa-software-custom"],
            ["label" => "Software Custom", "order" => 2, "is_visible" => true],
        );

        // Beri tempat di posisi kedua tanpa mengganda saat seeder diulang.
        if (NavItem::where("order", 2)->where("id", "!=", $item->id)->exists()) {
            NavItem::where("order", ">=", 2)->where("id", "!=", $item->id)->increment("order");
            $item->update(["order" => 2]);
        }
    }

    private function seedPositioning(): void
    {
        $settings = [
            "tagline" => "Jasa Website & Software Custom untuk Perusahaan",
            "description" => "Kami membangun website profesional dan software custom yang mengikuti cara kerja bisnis Anda, dari perusahaan berkembang hingga korporat.",
        ];
        foreach ($settings as $key => $value) {
            Setting::where("key", $key)->update(["value" => $value]);
        }
        Cache::forget("settings");
        Cache::forget("settings.grouped");

        $hero = PageSection::where("page", "home")->where("section_type", "hero")->first();
        if ($hero) {
            $hero->update([
                "title" => "Jasa Website & Software Custom untuk Perusahaan",
                "subtitle" => "Dari website yang membangun kepercayaan hingga software yang merapikan operasional, semuanya mengikuti cara kerja bisnis Anda.",
                "content" => array_merge($hero->content ?? [], ["eyebrow" => "Website & Software Custom"]),
            ]);
        }

        PageSection::where("page", "home")->where("section_type", "services")
            ->update(["title" => "Website dan Software untuk Setiap Kebutuhan"]);
    }
}
