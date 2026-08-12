<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageSection;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AboutController extends Controller
{
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
                    "vision" => null,
                    "values" => [],
                    "technologies" => [],
                ],
                "is_visible" => true,
                "order" => 0,
            ]
        );

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
            "content.vision" => "nullable|string",
            "content.values" => "nullable|array",
            "content.values.*.title" => "required_with:content.values|string|max:100",
            "content.values.*.description" => "nullable|string|max:500",
            "content.technologies" => "nullable|array",
            "content.technologies.*" => "string|max:100",
        ]);

        $section = PageSection::firstOrCreate(
            ["page" => "about", "section_type" => "about_main"]
        );
        $section->update($validated);

        return back()->with("success", "Halaman Tentang Kami berhasil diperbarui.");
    }
}
