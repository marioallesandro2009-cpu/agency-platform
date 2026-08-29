<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TechStack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class TechStackController extends Controller
{
    public function index()
    {
        return Inertia::render("Admin/TechStacks/Index", [
            "techStacks" => TechStack::ordered()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, requireIcon: true);

        if ($request->hasFile("icon")) {
            $data["icon_url"] = $this->storeIcon($request);
        }
        unset($data["icon"]);

        $data["order"] = (int) TechStack::max("order") + 1;

        TechStack::create($data);

        return back()->with("success", "Tech stack berhasil ditambahkan.");
    }

    public function update(Request $request, TechStack $techStack)
    {
        $data = $this->validated($request, requireIcon: false);

        if ($request->hasFile("icon")) {
            $data["icon_url"] = $this->storeIcon($request);
        }
        unset($data["icon"]);

        $techStack->update($data);

        return back()->with("success", "Tech stack berhasil diperbarui.");
    }

    public function destroy(TechStack $techStack)
    {
        $techStack->delete();

        return back()->with("success", "Tech stack berhasil dihapus.");
    }

    public function toggleActive(TechStack $techStack)
    {
        $techStack->update(["is_active" => ! $techStack->is_active]);

        return back();
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            "order" => "required|array",
            "order.*" => "required|integer|exists:tech_stacks,id",
        ]);

        foreach ($validated["order"] as $index => $id) {
            TechStack::where("id", $id)->update(["order" => $index + 1]);
        }

        return back();
    }

    private function validated(Request $request, bool $requireIcon): array
    {
        return $request->validate([
            "name" => "required|string|max:100",
            "link_url" => "nullable|url|max:255",
            "is_active" => "boolean",
            "icon" => ($requireIcon ? "required" : "nullable") . "|file|mimes:svg,png|max:512",
        ]);
    }

    // Custom logos (for stacks not on simple-icons) are stored alongside the
    // seeded brand SVGs in the same "icons" folder — kept separate from the
    // general Media library (admin.media.*) because that one only accepts
    // raster stock-photo formats (jpeg/png/gif/webp), not SVG.
    private function storeIcon(Request $request): string
    {
        $path = $request->file("icon")->store("icons", "public");

        return Storage::disk("public")->url($path);
    }
}
