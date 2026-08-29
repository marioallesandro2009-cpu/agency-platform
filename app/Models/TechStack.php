<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TechStack extends Model
{
    use HasFactory;

    protected $fillable = ["name", "icon_url", "link_url", "order", "is_active"];

    protected $casts = [
        "is_active" => "boolean",
    ];

    public function scopeActive($query)
    {
        return $query->where("is_active", true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy("order");
    }
}
