<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        "name", "slug", "category", "tier", "tagline", "description", "image", "price", "price_prefix", "price_suffix",
        "renewal_price", "estimated_days", "value_note", "features", "comparison_features", "badge", "is_recommended",
        "cta_text", "cta_url", "order", "is_published",
    ];

    protected $casts = [
        "features" => "array",
        "comparison_features" => "array",
        "is_recommended" => "boolean",
        "is_published" => "boolean",
        "price" => "integer",
        "renewal_price" => "integer",
    ];

    public function scopePublished($query)
    {
        return $query->where("is_published", true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy("order");
    }

    public function scopeForCategory($query, string $category)
    {
        return $query->where("category", $category);
    }
}