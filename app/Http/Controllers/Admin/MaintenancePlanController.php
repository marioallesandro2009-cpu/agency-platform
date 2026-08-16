<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MaintenancePlan;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class MaintenancePlanController extends Controller
{
    public function index()
    {
        return Inertia::render("Admin/MaintenancePlans/Index", [
            "plans" => MaintenancePlan::ordered()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data["slug"] = Str::slug($data["name"]);
        MaintenancePlan::create($data);

        return back()->with("success", "Paket maintenance berhasil ditambahkan.");
    }

    public function update(Request $request, MaintenancePlan $maintenancePlan)
    {
        $data = $this->validated($request);
        $data["slug"] = Str::slug($data["name"]);
        $maintenancePlan->update($data);

        return back()->with("success", "Paket maintenance berhasil diperbarui.");
    }

    public function destroy(MaintenancePlan $maintenancePlan)
    {
        $maintenancePlan->delete();

        return back()->with("success", "Paket maintenance berhasil dihapus.");
    }

    public function togglePublish(MaintenancePlan $maintenancePlan)
    {
        $maintenancePlan->update(["is_published" => ! $maintenancePlan->is_published]);

        return back();
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            "name" => "required|string|max:255",
            "price" => "required|integer",
            "original_price_monthly" => "nullable|integer",
            "price_yearly" => "nullable|integer",
            "original_price_yearly" => "nullable|integer",
            "features" => "nullable|array",
            "cta_text" => "nullable|string|max:100",
            "order" => "nullable|integer",
        ]);
    }
}
