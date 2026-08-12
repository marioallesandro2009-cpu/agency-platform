<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        "name", "company", "position", "photo", "testimonial", "rating", "is_published", "order",
    ];

    protected $casts = [
        "is_published" => "boolean",
        "rating" => "integer",
    ];

    public function scopePublished($query)
    {
        return $query->where("is_published", true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy("order");
    }
}