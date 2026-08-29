<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Support\HtmlSanitizer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BlogPostController extends Controller
{
    public function index()
    {
        return Inertia::render("Admin/Blog/Index", [
            "posts" => BlogPost::with("category")->latest()->get(),
            "categories" => BlogCategory::orderBy("name")->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);
        $validated["slug"] = ($validated["slug"] ?? null) ?: Str::slug($validated["title"]);
        $validated["author_id"] = $request->user()->id;

        if ($validated["status"] === "published" && empty($validated["published_at"])) {
            $validated["published_at"] = now();
        }

        BlogPost::create($validated);

        return back()->with("success", "Artikel berhasil ditambahkan.");
    }

    public function update(Request $request, BlogPost $blogPost)
    {
        $validated = $this->validateData($request, $blogPost->id);
        $validated["slug"] = ($validated["slug"] ?? null) ?: Str::slug($validated["title"]);

        if ($validated["status"] === "published" && empty($blogPost->published_at) && empty($validated["published_at"])) {
            $validated["published_at"] = now();
        }

        $blogPost->update($validated);

        return back()->with("success", "Artikel berhasil diperbarui.");
    }

    public function destroy(BlogPost $blogPost)
    {
        $blogPost->delete();

        return back()->with("success", "Artikel berhasil dihapus.");
    }

    private function validateData(Request $request, $ignoreId = null): array
    {
        $validated = $request->validate([
            "title" => "required|string|max:255",
            "slug" => "nullable|string|max:255|unique:blog_posts,slug," . $ignoreId,
            "blog_category_id" => "nullable|exists:blog_categories,id",
            "excerpt" => "nullable|string|max:500",
            "content" => "nullable|string",
            "featured_image" => "nullable|string|max:500",
            "featured_image_alt" => "required_with:featured_image|nullable|string|max:255",
            "status" => "required|in:draft,published,scheduled",
            "published_at" => "nullable|date",
            "seo_title" => "nullable|string|max:255",
            "seo_description" => "nullable|string|max:255",
            "focus_keyword" => "nullable|string|max:255",
        ]);

        $validated["content"] = HtmlSanitizer::clean($validated["content"] ?? null);

        return $validated;
    }
}