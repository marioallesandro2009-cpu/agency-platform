<?php

namespace App\Http\Controllers;

use App\Models\PageSection;
use Inertia\Inertia;

class AboutController extends Controller
{
    public function index()
    {
        $section = PageSection::where("page", "about")
            ->where("section_type", "about_main")
            ->first();

        return Inertia::render("Public/About", [
            "about" => $section ? [
                "title" => $section->title,
                "subtitle" => $section->subtitle,
                "image" => $section->image,
                "description" => $section->content["description"] ?? null,
                "mission" => $section->content["mission"] ?? null,
                "vision" => $section->content["vision"] ?? null,
                "values" => $section->content["values"] ?? [],
                "technologies" => $section->content["technologies"] ?? [],
            ] : null,
        ]);
    }
}