<?php

namespace Tests\Feature;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Support\Img;
use App\Support\WeeklyBlogImporter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeeklyBlogImportTest extends TestCase
{
    use RefreshDatabase;

    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dir = sys_get_temp_dir() . "/blog-mingguan-" . uniqid();
        mkdir($this->dir);
        BlogCategory::firstOrCreate(["slug" => "seo"], ["name" => "SEO"]);
    }

    protected function tearDown(): void
    {
        array_map("unlink", glob($this->dir . "/*") ?: []);
        @rmdir($this->dir);
        @unlink(public_path("images/blog/zz-uji-mingguan.jpg"));
        parent::tearDown();
    }

    private function sample(array $over = []): array
    {
        return array_merge([
            "title" => "5 Cara Uji Impor Artikel", "seo_title" => "5 Cara Uji Impor", "slug" => "uji-impor-mingguan",
            "category" => "seo", "focus_keyword" => "uji impor", "excerpt" => "Ringkasan uji.",
            "seo_description" => "Deskripsi uji.", "published_at" => "2026-11-01 07:00:00",
            "image" => "zz-uji-mingguan", "image_alt" => "Gambar uji untuk impor artikel mingguan",
            "content" => "<p>Isi</p>\n<h2>Bagian</h2>\n<p>Teks</p>",
        ], $over);
    }

    private function write(array $posts): void
    {
        file_put_contents($this->dir . "/2026-11-23.json", json_encode($posts));
    }

    private function image(): void
    {
        copy(public_path("images/blog/aplikasi-kontraktor.jpg"), public_path("images/blog/zz-uji-mingguan.jpg"));
    }

    public function test_imports_a_valid_post_as_published(): void
    {
        $this->image();
        $this->write([$this->sample()]);

        $result = WeeklyBlogImporter::run($this->dir);

        $this->assertSame(1, $result["imported"]);
        $post = BlogPost::where("slug", "uji-impor-mingguan")->first();
        $this->assertSame("published", $post->status);
        $this->assertSame("/images/blog/zz-uji-mingguan.jpg", $post->featured_image);
        $this->assertStringNotContainsString("\n", $post->content);
        $this->get("/blog/uji-impor-mingguan")->assertOk()->assertSee("5 Cara Uji Impor Artikel");
    }

    public function test_running_twice_does_not_duplicate(): void
    {
        $this->image();
        $this->write([$this->sample()]);

        WeeklyBlogImporter::run($this->dir);
        WeeklyBlogImporter::run($this->dir);

        $this->assertSame(1, BlogPost::where("slug", "uji-impor-mingguan")->count());
    }

    public function test_a_post_without_its_image_is_skipped_not_published(): void
    {
        $this->write([$this->sample(["image" => "gambar-yang-tidak-ada"])]);

        $result = WeeklyBlogImporter::run($this->dir);

        $this->assertSame(0, $result["imported"]);
        $this->assertStringContainsString("gambar", $result["skipped"][0]);
        $this->assertSame(0, BlogPost::where("slug", "uji-impor-mingguan")->count());
    }

    public function test_unknown_category_and_broken_json_are_reported_and_skipped(): void
    {
        $this->image();
        $this->write([$this->sample(["category" => "tidak-ada"])]);
        file_put_contents($this->dir . "/rusak.json", "{bukan json");

        $result = WeeklyBlogImporter::run($this->dir);

        $this->assertSame(0, $result["imported"]);
        $this->assertCount(2, $result["skipped"]);
    }

    public function test_a_future_publish_date_is_kept_off_the_blog_list_and_sitemap_until_it_arrives(): void
    {
        $this->image();
        $this->write([$this->sample(["published_at" => now()->addDays(3)->format("Y-m-d H:i:s")])]);

        WeeklyBlogImporter::run($this->dir);

        $this->get("/blog")->assertOk()->assertDontSee("5 Cara Uji Impor Artikel");
        $this->get("/sitemap.xml")->assertOk()->assertDontSee("uji-impor-mingguan", false);
    }

    public function test_publish_time_in_the_file_is_read_as_jakarta_time(): void
    {
        $this->image();
        $this->write([$this->sample(["published_at" => "2026-11-23 07:00:00"])]);

        WeeklyBlogImporter::run($this->dir);

        // 07:00 WIB is 00:00 UTC, so a Monday 07:00 batch is already visible a few hours later.
        $this->assertSame("2026-11-23 00:00:00", BlogPost::where("slug", "uji-impor-mingguan")->first()->published_at->setTimezone("UTC")->format("Y-m-d H:i:s"));
    }

    public function test_the_artisan_command_runs_on_an_empty_folder(): void
    {
        $this->artisan("blog:import")->assertExitCode(0);
    }

    public function test_a_single_jpg_without_webp_variants_still_renders(): void
    {
        $this->image();

        $this->assertSame("/images/blog/zz-uji-mingguan.jpg", Img::url("/images/blog/zz-uji-mingguan.jpg", 960));
        $this->assertSame("", Img::srcset("/images/blog/zz-uji-mingguan.jpg"));
        // Existing articles that do have variants keep the responsive set.
        $this->assertSame("/images/blog/aplikasi-kontraktor-960.webp", Img::url("/images/blog/aplikasi-kontraktor.jpg", 960));
        $this->assertStringContainsString("480w", Img::srcset("/images/blog/aplikasi-kontraktor.jpg"));
    }
}
