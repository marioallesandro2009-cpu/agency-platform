<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\BlogPost;

class BlogController extends Controller
{
    public function index()
    {
        return view("pages.blog.index", [
            "posts" => BlogPost::with("category")->published()->latestFirst()->get(),
            "categories" => BlogCategory::orderBy("name")->get(),
            "activeCategory" => null,
        ]);
    }

    public function category(BlogCategory $blogCategory)
    {
        return view("pages.blog.index", [
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

        return view("pages.blog.show", [
            "post" => $blogPost->load("category", "author"),
            "related" => $related,
        ]);
    }
}