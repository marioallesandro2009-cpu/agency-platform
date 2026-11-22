<?php

namespace Tests\Feature;

use App\Models\PortfolioProject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RealPortfolioTest extends TestCase
{
    use RefreshDatabase;

    private function runMigration(): void
    {
        (require database_path("migrations/2026_11_22_010000_add_real_client_projects_to_portfolio.php"))->up();
    }

    private function demo(int $order): PortfolioProject
    {
        return PortfolioProject::create([
            "name" => "Demo Konsep", "slug" => "demo-konsep", "category" => "Demo",
            "project_type" => "personal", "is_published" => true, "order" => $order,
        ]);
    }

    public function test_the_two_client_projects_exist_with_visit_links(): void
    {
        $ekasa = PortfolioProject::where("slug", "ekasa-cold-chain")->first();
        $jbm = PortfolioProject::where("slug", "jaya-berkat-melimpah")->first();

        $this->assertSame("https://ekasacoldchain.com", $ekasa->live_url);
        $this->assertSame("https://jbmelimpah.com", $jbm->live_url);
        $this->assertSame("client", $ekasa->project_type);
        $this->assertSame("client", $jbm->project_type);
        $this->assertTrue($ekasa->is_published && $jbm->is_published);
    }

    public function test_photos_ship_with_the_repo_as_webp(): void
    {
        foreach (["ekasa-cold-chain", "jaya-berkat-melimpah"] as $slug) {
            $path = public_path("images/portfolio/{$slug}.webp");

            $this->assertFileExists($path);
            $this->assertSame("image/webp", getimagesize($path)["mime"]);
            $this->assertSame("/images/portfolio/{$slug}.webp", PortfolioProject::where("slug", $slug)->value("thumbnail"));
        }
    }

    public function test_rerunning_neither_duplicates_nor_moves_the_demos_again(): void
    {
        $demo = $this->demo(3);

        $this->runMigration();
        $this->runMigration();

        $this->assertSame(1, PortfolioProject::where("slug", "ekasa-cold-chain")->count());
        $this->assertSame(1, PortfolioProject::where("slug", "jaya-berkat-melimpah")->count());
        $this->assertSame(3, $demo->fresh()->order);
    }

    public function test_on_a_database_without_them_the_demos_move_below_the_client_projects(): void
    {
        PortfolioProject::whereIn("slug", ["ekasa-cold-chain", "jaya-berkat-melimpah"])->delete();
        $demo = $this->demo(1);

        $this->runMigration();

        $this->assertSame(3, $demo->fresh()->order);
        $this->assertSame(["ekasa-cold-chain", "jaya-berkat-melimpah", "demo-konsep"], PortfolioProject::ordered()->pluck("slug")->all());
    }

    public function test_portfolio_page_lists_client_projects_first_without_the_demo_label(): void
    {
        $this->demo(3);

        $response = $this->get("/portfolio")->assertOk();
        $html = $response->getContent();

        $response->assertSeeInOrder(["PT Ekasa Cold Chain", "PT Jaya Berkat Melimpah", "Demo Konsep"])
            ->assertSee('href="https://ekasacoldchain.com"', false)
            ->assertSee('href="https://jbmelimpah.com"', false);
        $this->assertSame(1, substr_count($html, "Contoh Kapabilitas"), "Only the demo project carries the label.");

        foreach (["ekasa-cold-chain", "jaya-berkat-melimpah"] as $slug) {
            [$w, $h] = getimagesize(public_path("images/portfolio/{$slug}.webp"));
            $response->assertSee("aspect-ratio: {$w} / {$h}", false);
        }

        // The grayscale "art-frame" look is for demo stock photos only; client screenshots keep their real colors.
        $this->assertSame(1, substr_count($html, "art-frame"), "Only the demo project uses the art-frame filter.");
    }
}
