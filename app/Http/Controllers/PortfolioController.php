<?php

namespace App\Http\Controllers;

use App\Models\PortfolioProject;

class PortfolioController extends Controller
{
    public function index()
    {
        return view("pages.portfolio.index", [
            "projects" => PortfolioProject::published()->ordered()->get([
                "id", "name", "slug", "category", "short_description", "thumbnail", "is_featured", "project_type", "live_url",
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

        return view("pages.portfolio.show", [
            "project" => $portfolioProject,
            "related" => $related,
        ]);
    }
}