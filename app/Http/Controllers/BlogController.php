<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use Inertia\Inertia;

class BlogController extends Controller
{
    public function index()
    {
        return Inertia::render("Public/Blog/Index", [
            "posts" => BlogPost::with("category")->published()->latestFirst()->get(),
            "categories" => BlogCategory::orderBy("name")->get(),
        ]);
    }

    public function category(BlogCategory $blogCategory)
    {
        return Inertia::render("Public/Blog/Index", [
            "posts" => BlogPost::with("category")
                ->where("blog_category_id", $blogCategory->id)
                ->published()
                ->latestFirst()
                ->get(),
            "categories" => BlogCategory::orderBy("name")->get(),
            "activeCategory" => $blogCategory,
        ]);
    }

    public function show(BlogPost $blogPost)
    {
        abort_unless($blogPost->status === "published", 404);

        $related = BlogPost::where("id", "!=", $blogPost->id)
            ->where("blog_category_id", $blogPost->blog_category_id)
            ->published()
            ->latestFirst()
            ->take(3)
            ->get();

        return Inertia::render("Public/Blog/Show", [
            "post" => $blogPost->load("category", "author"),
            "related" => $related,
        ]);
    }
}