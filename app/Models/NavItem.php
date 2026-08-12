<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NavItem extends Model
{
    protected $fillable = ["label", "url", "order", "is_visible"];

    protected $casts = ["is_visible" => "boolean"];

    public function scopeVisible($query)
    {
        return $query->where("is_visible", true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy("order");
    }
}
