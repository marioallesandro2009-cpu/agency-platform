<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenancePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        "name", "slug", "price", "price_yearly", "features", "cta_text", "order", "is_published",
    ];

    protected $casts = [
        "features" => "array",
        "is_published" => "boolean",
        "price" => "integer",
        "price_yearly" => "integer",
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
