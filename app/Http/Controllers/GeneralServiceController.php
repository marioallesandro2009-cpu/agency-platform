<?php

namespace App\Http\Controllers;

use App\Models\PortfolioProject;
use App\Models\PricingPackage;
use App\Models\Service;
use Inertia\Inertia;

class GeneralServiceController extends Controller
{
    public function index()
    {
        return Inertia::render("Public/JasaWebsite", [
            "services" => Service::published()->ordered()->get(),
            "portfolioProjects" => PortfolioProject::published()->ordered()->limit(3)->get([
                "id", "name", "slug", "category", "thumbnail",
            ]),
            "pricingPackages" => PricingPackage::published()->ordered()->get(),
        ]);
    }
}