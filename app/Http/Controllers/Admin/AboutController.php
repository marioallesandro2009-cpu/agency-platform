<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSection;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AboutController extends Controller
{
    // Admin can only relabel or hide each stats row — the number itself is
    // always computed live from real data in the public AboutController, so
    // there's no way to type in a fabricated stat here.
    private const DEFAULT_STATS = [
        ["key" => "portfolio", "label" => "Proyek Portfolio", "is_visible" => true],
        ["key" => "blog", "label" => "Artikel Blog", "is_visible" => true],
        ["key" => "techstack", "label" => "Tech Stack Digunakan", "is_visible" => true],
    ];

    public function edit()
    {
        $section = PageSection::firstOrCreate(
            ["page" => "about", "section_type" => "about_main"],
            [
                "title" => "Tentang Kami",
                "subtitle" => "Siapa Kami",
                "content" => [
                    "description" => null,
                    "mission" => null,
                    "mission_icon" => null,
                    "vision" => null,
                    "vision_icon" => null,
                    "values" => [],
                    "stats" => self::DEFAULT_STATS,
                ],
                "is_visible" => true,
                "order" => 0,
            ]
        );

        // Pre-existing rows (created before "stats" existed) won't have it
        // in their stored content — fall back to the default set instead of
        // showing an empty admin form.
        if (empty($section->content["stats"])) {
            $section->content = [...$section->content, "stats" => self::DEFAULT_STATS];
        }

        return Inertia::render("Admin/About/Edit", [
            "section" => $section,
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            "title" => "nullable|string|max:255",
            "subtitle" => "nullable|string|max:255",
            "image" => "nullable|string|max:500",
            "content" => "nullable|array",
            "content.description" => "nullable|string",
            "content.mission" => "nullable|string",
            "content.mission_icon" => "nullable|string|max:100",
            "content.vision" => "nullable|string",
            "content.vision_icon" => "nullable|string|max:100",
            "content.values" => "nullable|array",
            "content.values.*.title" => "required_with:content.values|string|max:100",
            "content.values.*.description" => "nullable|string|max:500",
            "content.values.*.icon" => "nullable|string|max:100",
            "content.stats" => "nullable|array",
            "content.stats.*.key" => "required_with:content.stats|string|max:50",
            "content.stats.*.label" => "required_with:content.stats|string|max:100",
            "content.stats.*.is_visible" => "boolean",
        ]);

        $section = PageSection::firstOrCreate(
            ["page" => "about", "section_type" => "about_main"]
        );
        $section->update($validated);

        return back()->with("success", "Halaman Tentang Kami berhasil diperbarui.");
    }
}
