<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSection;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PageSectionController extends Controller
{
    public function index()
    {
        $sections = PageSection::forPage("home")->ordered()->get();

        return Inertia::render("Admin/PageSections/Index", [
            "sections" => $sections,
        ]);
    }

    public function toggle(PageSection $pageSection)
    {
        $pageSection->update([
            "is_visible" => ! $pageSection->is_visible,
        ]);

        return back();
    }

    public function update(Request $request, PageSection $pageSection)
    {
        $validated = $request->validate([
            "title" => "nullable|string|max:255",
            "subtitle" => "nullable|string|max:255",
            "image" => "nullable|string|max:500",
            "content" => "nullable|array",
            "content.description" => "nullable|string",
            "content.button_text" => "nullable|string|max:100",
            "content.button_url" => "nullable|string|max:255",
            "content.extra_note" => "nullable|string|max:100",
            // "why_us" cards (content.items[].icon added alongside title/description)
            "content.items" => "nullable|array",
            "content.items.*.title" => "required_with:content.items|string|max:100",
            "content.items.*.description" => "nullable|string|max:500",
            "content.items.*.icon" => "nullable|string|max:100",
            // "hero" fields
            "content.eyebrow" => "nullable|string|max:100",
            "content.cta_primary_text" => "nullable|string|max:100",
            "content.cta_primary_url" => "nullable|string|max:255",
            "content.cta_secondary_text" => "nullable|string|max:100",
            "content.cta_secondary_url" => "nullable|string|max:255",
        ]);

        $pageSection->update($validated);

        return back()->with("success", "Section berhasil diperbarui.");
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            "order" => "required|array",
            "order.*" => "required|integer|exists:page_sections,id",
        ]);

        foreach ($validated["order"] as $index => $id) {
            PageSection::where("id", $id)->update(["order" => $index + 1]);
        }

        return back();
    }
}