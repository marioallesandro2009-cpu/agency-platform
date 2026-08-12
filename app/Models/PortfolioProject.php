<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortfolioProject extends Model
{
    use HasFactory;

    protected $fillable = [
        "name", "slug", "category", "short_description", "description",
        "challenge", "solution", "features", "technologies", "client_label",
        "thumbnail", "cover_image", "gallery", "live_url",
        "seo_title", "seo_description", "is_featured", "is_published", "order",
    ];

    protected $casts = [
        "features" => "array",
        "technologies" => "array",
        "gallery" => "array",
        "is_featured" => "boolean",
        "is_published" => "boolean",
    ];

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