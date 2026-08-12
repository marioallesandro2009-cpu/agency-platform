<?php

namespace App\Http\Controllers;

use App\Models\PortfolioProject;
use Inertia\Inertia;

class PortfolioController extends Controller
{
    public function index()
    {
        return Inertia::render("Public/Portfolio/Index", [
            "projects" => PortfolioProject::published()->ordered()->get([
                "id", "name", "slug", "category", "short_description", "thumbnail", "is_featured",
            ]),
        ]);
    }

    public function show(PortfolioProject $portfolioProject)
    {
        abort_unless($portfolioProject->is_published, 404);

        $related = PortfolioProject::published()
            ->where("id", "!=", $portfolioProject->id)
            ->where("category", $portfolioProject->category)
            ->ordered()
            ->limit(3)
            ->get(["id", "name", "slug", "category", "thumbnail"]);

        return Inertia::render("Public/Portfolio/Show", [
            "project" => $portfolioProject,
            "related" => $related,
        ]);
    }
}