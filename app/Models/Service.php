<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        "name", "slug", "short_description", "badge_label", "description", "long_description", "icon", "image",
        "features", "benefits", "problems", "detail_features", "target_audience", "process_steps", "faqs",
        "cta_text", "cta_url", "is_featured", "order", "is_published", "show_portfolio", "blog_category_id",
        "seo_title", "seo_description",
    ];

    protected $casts = [
        "features" => "array",
        "benefits" => "array",
        "problems" => "array",
        "detail_features" => "array",
        "process_steps" => "array",
        "faqs" => "array",
        "is_featured" => "boolean",
        "is_published" => "boolean",
        "show_portfolio" => "boolean",
    ];

    public function blogCategory()
    {
        return $this->belongsTo(BlogCategory::class);
    }

    public function portfolioProjects()
    {
        return $this->hasMany(PortfolioProject::class);
    }

    public function scopePublished($query)
    {
        return $query->where("is_published", true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy("order");
    }

    public function getRouteKeyName(): string
    {
        return "slug";
    }
}