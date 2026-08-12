<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PricingPackage;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PricingController extends Controller
{
    public function index()
    {
        return Inertia::render("Admin/Pricing/Index", [
            "packages" => PricingPackage::ordered()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data["slug"] = \Illuminate\Support\Str::slug($data["category"]."-".$data["tier"]);
        PricingPackage::create($data);

        return back()->with("success", "Paket harga berhasil ditambahkan.");
    }

    public function update(Request $request, PricingPackage $pricingPackage)
    {
        $data = $this->validated($request, $pricingPackage->id);
        $data["slug"] = \Illuminate\Support\Str::slug($data["category"]."-".$data["tier"]);
        $pricingPackage->update($data);

        return back()->with("success", "Paket harga berhasil diperbarui.");
    }

    public function destroy(PricingPackage $pricingPackage)
    {
        $pricingPackage->delete();

        return back()->with("success", "Paket harga berhasil dihapus.");
    }

    public function togglePublish(PricingPackage $pricingPackage)
    {
        $pricingPackage->update(["is_published" => ! $pricingPackage->is_published]);

        return back();
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            "name" => "required|string|max:255",
            "category" => ["required", "string", Rule::in(["landing_page", "company_profile", "ecommerce", "sistem_informasi"])],
            "tier" => [
                "required", "string", Rule::in(["seed", "growth", "scale"]),
                Rule::unique("pricing_packages", "tier")->where(fn ($query) => $query->where("category", $request->input("category")))->ignore($ignoreId),
            ],
            "tagline" => "nullable|string|max:255",
            "description" => "nullable|string",
            "image" => "nullable|string|max:500",
            "price" => "nullable|integer",
            "price_prefix" => "nullable|string|max:100",
            "price_suffix" => "nullable|string|max:100",
            "renewal_price" => "nullable|integer",
            "features" => "nullable|array",
            "badge" => "nullable|string|max:100",
            "is_recommended" => "boolean",
            "cta_text" => "nullable|string|max:100",
            "cta_url" => "nullable|string|max:255",
            "order" => "nullable|integer",
        ]);
    }
}