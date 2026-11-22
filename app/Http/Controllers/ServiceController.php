<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\PortfolioProject;
use App\Models\Service;

class ServiceController extends Controller
{
    public function show(Service $service)
    {
        abort_unless($service->is_published, 404);

        $relatedPosts = collect();
        if ($service->blog_category_id) {
            $relatedPosts = BlogPost::where("blog_category_id", $service->blog_category_id)
                ->published()
                ->latestFirst()
                ->take(4)
                ->get(["id", "title", "slug", "excerpt", "featured_image", "featured_image_alt", "published_at"]);
        }

        // Software Custom has its own blue landing page; every other service
        // shares the generic detail template.
        $view = $service->slug === "jasa-software-custom" ? "pages.custom-software" : "pages.service-detail";

        return view($view, [
            "service" => $service,
            "otherServices" => Service::published()
                ->where("id", "!=", $service->id)
                ->ordered()
                ->get(["id", "name", "slug", "short_description", "icon", "image"]),
            "relatedPosts" => $relatedPosts,
            "relatedPortfolio" => $service->show_portfolio
                ? PortfolioProject::published()
                    ->where("service_id", $service->id)
                    ->orderByDesc("is_featured")
                    ->ordered()
                    ->limit(3)
                    ->get(["id", "name", "slug", "category", "short_description", "thumbnail", "live_url"])
                : collect(),
        ]);
    }
}