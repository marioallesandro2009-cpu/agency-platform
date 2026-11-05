<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\PortfolioProject;
use App\Models\Service;
use Illuminate\Support\Facades\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = [];

        foreach ([
            ["route" => "home", "priority" => "1.0"],
            ["route" => "pricing", "priority" => "0.8"],
            ["route" => "jasa-website", "priority" => "0.8"],
            ["route" => "portfolio.index", "priority" => "0.7"],
            ["route" => "about", "priority" => "0.5"],
            ["route" => "faq", "priority" => "0.5"],
            ["route" => "blog.index", "priority" => "0.7"],
            ["route" => "konsultasi", "priority" => "0.6"],
            ["route" => "kontak", "priority" => "0.6"],
        ] as $page) {
            $urls[] = ["loc" => route($page["route"]), "lastmod" => null, "priority" => $page["priority"]];
        }

        $serviceRoutes = [
            "jasa-website-umkm" => "service.umkm",
            "jasa-website-company-profile" => "service.company-profile",
            "jasa-toko-online" => "service.toko-online",
            "jasa-web-development" => "service.web-development",
            "jasa-software-custom" => "service.custom-software",
        ];

        foreach (Service::published()->get() as $service) {
            if (! isset($serviceRoutes[$service->slug])) {
                continue;
            }
            $urls[] = ["loc" => route($serviceRoutes[$service->slug]), "lastmod" => $service->updated_at, "priority" => "0.8"];
        }

        foreach (BlogCategory::all() as $category) {
            $urls[] = ["loc" => route("blog.category", $category), "lastmod" => null, "priority" => "0.5"];
        }

        foreach (BlogPost::published()->get() as $post) {
            $urls[] = ["loc" => route("blog.show", $post), "lastmod" => $post->updated_at, "priority" => "0.6"];
        }

        foreach (PortfolioProject::published()->get() as $project) {
            $urls[] = ["loc" => route("portfolio.show", $project), "lastmod" => $project->updated_at, "priority" => "0.6"];
        }

        // Generate XML secara manual (tanpa Blade) supaya tidak bergantung
        // pada view compiler yang bermasalah di production.
        $xml = '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($url["loc"], ENT_XML1 | ENT_QUOTES) . "</loc>\n";

            if (! empty($url["lastmod"])) {
                $lastmod = is_object($url["lastmod"]) ? $url["lastmod"]->toAtomString() : $url["lastmod"];
                $xml .= "    <lastmod>" . $lastmod . "</lastmod>\n";
            }

            $xml .= "    <priority>" . $url["priority"] . "</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= "</urlset>";

        return Response::make($xml, 200, ["Content-Type" => "application/xml"]);
    }

    public function robots()
    {
        $content = implode("\n", [
            "User-agent: *",
            "Disallow: /admin",
            "Disallow: /dashboard",
            "Disallow: /profile",
            "Allow: /",
            "",
            "Sitemap: " . route("sitemap"),
        ]);

        return Response::make($content, 200, ["Content-Type" => "text/plain"]);
    }
}
