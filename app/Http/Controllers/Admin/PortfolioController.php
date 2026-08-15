<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortfolioProject;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class PortfolioController extends Controller
{
    public function index()
    {
        return Inertia::render("Admin/Portfolio/Index", [
            "projects" => PortfolioProject::ordered()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);
        $validated["slug"] = ($validated["slug"] ?? null) ?: Str::slug($validated["name"]);

        PortfolioProject::create($validated);

        return back()->with("success", "Proyek portfolio berhasil ditambahkan.");
    }

    public function update(Request $request, PortfolioProject $portfolioProject)
    {
        $validated = $this->validateData($request, $portfolioProject->id);
        $validated["slug"] = ($validated["slug"] ?? null) ?: Str::slug($validated["name"]);

        $portfolioProject->update($validated);

        return back()->with("success", "Proyek portfolio berhasil diperbarui.");
    }

    public function destroy(PortfolioProject $portfolioProject)
    {
        $portfolioProject->delete();

        return back()->with("success", "Proyek portfolio berhasil dihapus.");
    }

    public function togglePublish(PortfolioProject $portfolioProject)
    {
        $portfolioProject->update(["is_published" => ! $portfolioProject->is_published]);

        return back();
    }

    private function validateData(Request $request, $ignoreId = null): array
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "slug" => "nullable|string|max:255|unique:portfolio_projects,slug," . $ignoreId,
            "category" => "nullable|string|max:255",
            "short_description" => "nullable|string|max:255",
            "description" => "nullable|string",
            "challenge" => "nullable|string",
            "solution" => "nullable|string",
            "features" => "nullable|string",
            "technologies" => "nullable|string",
            "client_label" => "nullable|string|max:255",
            "project_type" => "required|in:client,personal",
            "thumbnail" => "nullable|string|max:500",
            "cover_image" => "nullable|string|max:500",
            "gallery" => "nullable|string",
            "live_url" => "nullable|url|max:500",
            "seo_title" => "nullable|string|max:255",
            "seo_description" => "nullable|string|max:255",
            "is_featured" => "boolean",
            "is_published" => "boolean",
            "order" => "nullable|integer",
        ]);

        return array_merge($validated, $this->parseListFields($request));
    }

    private function parseListFields(Request $request): array
    {
        return [
            "features" => $request->filled("features")
                ? array_map("trim", explode(",", $request->input("features")))
                : [],
            "technologies" => $request->filled("technologies")
                ? array_map("trim", explode(",", $request->input("technologies")))
                : [],
            "gallery" => $request->filled("gallery")
                ? array_map("trim", explode(",", $request->input("gallery")))
                : [],
        ];
    }
}