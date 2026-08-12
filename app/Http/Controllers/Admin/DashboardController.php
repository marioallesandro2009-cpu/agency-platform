<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Lead;
use App\Models\PageVisit;
use App\Models\PortfolioProject;
use App\Models\Service;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $today = now()->toDateString();

        $visitsLast7Days = PageVisit::selectRaw("visit_date, COUNT(*) as total")
            ->where("visit_date", ">=", now()->subDays(6)->toDateString())
            ->groupBy("visit_date")
            ->orderBy("visit_date")
            ->get();

        return Inertia::render("Admin/Dashboard", [
            "stats" => [
                "leads" => Lead::count(),
                "portfolio" => PortfolioProject::count(),
                "services" => Service::count(),
                "blogPublished" => BlogPost::published()->count(),
                "blogDraft" => BlogPost::where("status", "draft")->count(),
                "visitorsToday" => PageVisit::where("visit_date", $today)->distinct("session_id")->count("session_id"),
                "visitorsTotal" => PageVisit::distinct("session_id")->count("session_id"),
            ],
            "visitorsChart" => $visitsLast7Days,
        ]);
    }

    public function resetVisitors()
    {
        PageVisit::query()->delete();

        return back()->with("success", "Data pengunjung sudah direset.");
    }
}