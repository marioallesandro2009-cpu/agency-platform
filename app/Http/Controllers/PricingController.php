<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\MaintenancePlan;
use App\Models\PricingPackage;
use Inertia\Inertia;

class PricingController extends Controller
{
    public function index()
    {
        return Inertia::render("Public/Pricing", [
            "packages" => PricingPackage::published()->ordered()->get(),
            "maintenancePlans" => MaintenancePlan::published()->ordered()->get(),
            "recentActivity" => Lead::recentTickerActivity(),
        ]);
    }
}