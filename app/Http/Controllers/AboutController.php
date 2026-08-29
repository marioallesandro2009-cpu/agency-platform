<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\PageSection;
use App\Models\PortfolioProject;
use App\Models\TechStack;
use App\Models\Testimonial;
use Inertia\Inertia;

class AboutController extends Controller
{
    public function index()
    {
        $section = PageSection::where("page", "about")
            ->where("section_type", "about_main")
            ->first();

        return Inertia::render("Public/About", [
            "about" => $section ? [
                "title" => $section->title,
                "subtitle" => $section->subtitle,
                "image" => $section->image,
                "description" => $section->content["description"] ?? null,
                "mission" => $section->content["mission"] ?? null,
                "mission_icon" => $section->content["mission_icon"] ?? null,
                "vision" => $section->content["vision"] ?? null,
                "vision_icon" => $section->content["vision_icon"] ?? null,
                "values" => $section->content["values"] ?? [],
            ] : null,
            // Replaces the old $section->content['technologies'] string list
            // (still stored on PageSection but no longer read here) — logos
            // are now a dedicated, admin-manageable resource (see
            // admin.tech-stacks.*) instead of plain text pills.
            "techStacks" => TechStack::active()->ordered()->get(["name", "icon_url", "link_url"]),
            "stats" => $this->visibleStats($section->content["stats"] ?? $this->defaultStatsConfig()),
        ]);
    }

    // Mirrors Admin\AboutController::DEFAULT_STATS — used here so a
    // pre-existing PageSection row (saved before "stats" existed) still
    // shows the stat bar instead of nothing until an admin re-saves it.
    private function defaultStatsConfig(): array
    {
        return [
            ["key" => "portfolio", "label" => "Proyek Portfolio", "is_visible" => true],
            ["key" => "blog", "label" => "Artikel Blog", "is_visible" => true],
            ["key" => "techstack", "label" => "Tech Stack Digunakan", "is_visible" => true],
            ["key" => "rating", "label" => "Rating Klien", "is_visible" => true],
        ];
    }

    /**
     * Admin only controls each stat's label/visibility — the value is
     * always computed here from real data, so the page can never show a
     * fabricated number. A stat with no real data yet (e.g. zero
     * testimonials) is dropped rather than showing a hollow "0".
     */
    private function visibleStats(array $statsConfig): array
    {
        return collect($statsConfig)
            ->filter(fn ($stat) => $stat["is_visible"] ?? true)
            ->map(fn ($stat) => [
                "label" => $stat["label"] ?? "",
                "value" => $this->computeStatValue($stat["key"] ?? ""),
            ])
            ->filter(fn ($stat) => filled($stat["value"]))
            ->values()
            ->all();
    }

    private function computeStatValue(string $key): ?string
    {
        $value = match ($key) {
            "portfolio" => PortfolioProject::published()->count(),
            "blog" => BlogPost::published()->count(),
            "techstack" => TechStack::active()->count(),
            "rating" => Testimonial::published()->avg("rating"),
            default => null,
        };

        if (!$value) {
            return null;
        }

        return $key === "rating" ? number_format($value, 1) : (string) $value;
    }
}