<?php

use App\Models\PortfolioProject;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    private const SLUGS = ["ekasa-cold-chain", "jaya-berkat-melimpah"];

    /**
     * Adds the first two real client websites to the portfolio, ahead of the
     * existing demo projects. Safe to run on a database that already has them:
     * rows are matched by slug and the demos are only moved down once.
     * Photos ship with the repo in public/images/portfolio (storage/ is not deployed).
     */
    public function up(): void
    {
        if (PortfolioProject::whereIn("slug", self::SLUGS)->doesntExist()) {
            PortfolioProject::query()->increment("order", count(self::SLUGS));
        }

        $projects = [
            [
                "slug" => "ekasa-cold-chain",
                "name" => "PT Ekasa Cold Chain",
                "short_description" => "Website company profile dan panel admin untuk layanan penyimpanan dan pengiriman berpendingin.",
                "thumbnail" => "/images/portfolio/ekasa-cold-chain.webp",
                "live_url" => "https://ekasacoldchain.com",
                "order" => 1,
            ],
            [
                "slug" => "jaya-berkat-melimpah",
                "name" => "PT Jaya Berkat Melimpah",
                "short_description" => "Website company profile dan panel admin untuk perusahaan perikanan dan hasil laut.",
                "thumbnail" => "/images/portfolio/jaya-berkat-melimpah.webp",
                "live_url" => "https://jbmelimpah.com",
                "order" => 2,
            ],
        ];

        foreach ($projects as $project) {
            PortfolioProject::updateOrCreate(["slug" => $project["slug"]], $project + [
                "category" => "Company Profile",
                "description" => $project["short_description"],
                "client_label" => $project["name"],
                "project_type" => "client",
                "cover_image" => $project["thumbnail"],
                "gallery" => [],
                "is_featured" => true,
                "is_published" => true,
            ]);
        }
    }

    public function down(): void
    {
        if (PortfolioProject::whereIn("slug", self::SLUGS)->delete() > 0) {
            PortfolioProject::query()->where("order", ">=", count(self::SLUGS))->decrement("order", count(self::SLUGS));
        }
    }
};
