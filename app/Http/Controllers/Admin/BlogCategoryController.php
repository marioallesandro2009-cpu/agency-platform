<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            "name" => "required|string|max:255",
            "description" => "nullable|string|max:500",
        ]);
        $validated["slug"] = Str::slug($validated["name"]);

        BlogCategory::create($validated);

        return back()->with("success", "Kategori berhasil ditambahkan.");
    }

    public function destroy(BlogCategory $blogCategory)
    {
        $blogCategory->delete();

        return back()->with("success", "Kategori berhasil dihapus.");
    }
}