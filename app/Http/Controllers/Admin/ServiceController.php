<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\Service;
use App\Support\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index()
    {
        return Inertia::render("Admin/Services/Index", [
            "services" => Service::with("blogCategory")->ordered()->get(),
            "blogCategories" => BlogCategory::orderBy("name")->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data["slug"] = Str::slug($data["name"]);
        Service::create($data);

        return back()->with("success", "Layanan berhasil ditambahkan.");
    }

    public function update(Request $request, Service $service)
    {
        $data = $this->validated($request);
        $service->update($data);

        return back()->with("success", "Layanan berhasil diperbarui.");
    }

    public function destroy(Service $service)
    {
        $service->delete();

        return back()->with("success", "Layanan berhasil dihapus.");
    }

    public function togglePublish(Service $service)
    {
        $service->update(["is_published" => ! $service->is_published]);

        return back();
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "short_description" => "nullable|string|max:500",
            "description" => "nullable|string",
            "long_description" => "nullable|string",
            "icon" => "nullable|string|max:100",
            "image" => "nullable|string|max:500",
            "features" => "nullable|array",
            "benefits" => "nullable|array",
            "cta_text" => "nullable|string|max:100",
            "cta_url" => "nullable|string|max:255",
            "is_featured" => "boolean",
            "show_portfolio" => "boolean",
            "blog_category_id" => "nullable|exists:blog_categories,id",
            "order" => "nullable|integer",
            "seo_title" => "nullable|string|max:255",
            "seo_description" => "nullable|string|max:255",
        ]);

        $validated["description"] = HtmlSanitizer::clean($validated["description"] ?? null);
        $validated["long_description"] = HtmlSanitizer::clean($validated["long_description"] ?? null);

        return $validated;
    }
}