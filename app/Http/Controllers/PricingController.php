<?php

namespace App\Http\Controllers;

use App\Models\MaintenancePlan;
use App\Models\PricingPackage;

class PricingController extends Controller
{
    public function index()
    {
        return view("pages.pricing", [
            "packages" => PricingPackage::published()->ordered()->get(),
            "maintenancePlans" => MaintenancePlan::published()->ordered()->get(),
        ]);
    }
}