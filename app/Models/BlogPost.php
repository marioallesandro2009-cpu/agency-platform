<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlogPost extends Model
{
    use HasFactory;

    protected $fillable = [
        "blog_category_id", "author_id", "title", "slug", "excerpt", "content",
        "featured_image", "featured_image_alt", "status", "published_at",
        "seo_title", "seo_description", "focus_keyword",
    ];

    protected $casts = [
        "published_at" => "datetime",
    ];

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, "blog_category_id");
    }

    public function author()
    {
        return $this->belongsTo(User::class, "author_id");
    }

    public function scopePublished($query)
    {
        return $query->where("status", "published")
            ->where(function ($q) {
                $q->whereNull("published_at")->orWhere("published_at", "<=", now());
            });
    }

    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc("published_at");
    }

    public function getRouteKeyName(): string
    {
        return "slug";
    }
}