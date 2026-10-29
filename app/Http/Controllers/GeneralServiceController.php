<?php

namespace App\Http\Controllers;

use App\Models\PortfolioProject;
use App\Models\PricingPackage;
use App\Models\Service;

class GeneralServiceController extends Controller
{
    public function index()
    {
        return view("pages.jasa-website", [
            "services" => Service::published()->ordered()->get(),
            "portfolioProjects" => PortfolioProject::published()->ordered()->limit(3)->get([
                "id", "name", "slug", "category", "thumbnail",
            ]),
            "pricingPackages" => PricingPackage::published()->ordered()->get(),
        ]);
    }
}