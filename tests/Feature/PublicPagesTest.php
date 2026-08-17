<?php

namespace Tests\Feature;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\PortfolioProject;
use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @dataProvider publicRouteProvider
     */
    public function test_public_page_loads_successfully(string $uri): void
    {
        $response = $this->get($uri);

        $response->assertOk();
    }

    public static function publicRouteProvider(): array
    {
        return [
            'home' => ['/'],
            'pricing' => ['/harga'],
            'jasa-website overview' => ['/jasa-website'],
            'portfolio index' => ['/portfolio'],
            'about' => ['/tentang-kami'],
            'faq' => ['/faq'],
            'blog index' => ['/blog'],
            'konsultasi' => ['/konsultasi'],
            'kontak' => ['/kontak'],
        ];
    }

    /**
     * @dataProvider serviceRouteProvider
     */
    public function test_service_detail_page_loads_when_published(string $uri, string $slug): void
    {
        Service::create(['name' => 'Test Service', 'slug' => $slug, 'is_published' => true, 'order' => 1]);

        $this->get($uri)->assertOk();
    }

    public static function serviceRouteProvider(): array
    {
        return [
            'jasa-website-umkm' => ['/jasa-website-umkm', 'jasa-website-umkm'],
            'jasa-website-company-profile' => ['/jasa-website-company-profile', 'jasa-website-company-profile'],
            'jasa-toko-online' => ['/jasa-toko-online', 'jasa-toko-online'],
            'jasa-web-development' => ['/jasa-web-development', 'jasa-web-development'],
        ];
    }

    public function test_unpublished_service_returns_404(): void
    {
        Service::create([
            'name' => 'Draft Service', 'slug' => 'jasa-website-umkm',
            'is_published' => false, 'order' => 1,
        ]);

        $this->get('/jasa-website-umkm')->assertNotFound();
    }

    public function test_blog_post_detail_page_loads(): void
    {
        $category = BlogCategory::create(['name' => 'Tips', 'slug' => 'tips']);
        $post = BlogPost::create([
            'title' => 'Contoh Artikel',
            'slug' => 'contoh-artikel',
            'blog_category_id' => $category->id,
            'content' => '<p>Isi artikel</p>',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $this->get('/blog/' . $post->slug)->assertOk();
    }

    public function test_unpublished_blog_post_returns_404(): void
    {
        $post = BlogPost::create([
            'title' => 'Draft Artikel',
            'slug' => 'draft-artikel',
            'content' => '<p>Isi</p>',
            'status' => 'draft',
        ]);

        $this->get('/blog/' . $post->slug)->assertNotFound();
    }

    public function test_portfolio_detail_page_loads(): void
    {
        $project = PortfolioProject::create([
            'name' => 'Contoh Proyek',
            'slug' => 'contoh-proyek',
            'category' => 'UMKM',
            'is_published' => true,
            'order' => 1,
        ]);

        $this->get('/portfolio/' . $project->slug)->assertOk();
    }

    public function test_sitemap_and_robots_respond(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertHeader('content-type', 'application/xml');
        $this->get('/robots.txt')->assertOk();
    }

    public function test_sitemap_xml_declaration_is_the_first_bytes_of_the_response(): void
    {
        // Regression guard: a raw XML declaration written literally as the
        // first line of a Blade view gets compiled into a PHP file where
        // PHP's parser chokes on it (short_open_tag), producing a 500 in
        // production even though local view()->render() calls can succeed.
        // The declaration must be emitted via a Blade raw-echo, with
        // nothing (no whitespace, no BOM) before it.
        $content = $this->get('/sitemap.xml')->assertOk()->getContent();

        $declaration = '<?xml version="1.0" encoding="UTF-8"?>';
        $this->assertSame($declaration, substr($content, 0, strlen($declaration)));
        $this->assertStringStartsWith('<?xml', $content);
    }
}
