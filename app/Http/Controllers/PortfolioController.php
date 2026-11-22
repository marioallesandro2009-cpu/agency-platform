<?php

namespace App\Http\Controllers;

use App\Models\PortfolioProject;

/**
 * The portfolio is a gallery only: a photo and a visit link per project.
 * There is no detail page; old /portfolio/{slug} URLs redirect here (routes/web.php).
 */
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
}
