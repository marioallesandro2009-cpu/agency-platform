<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MascotAsset extends Model
{
    protected $fillable = ["placement_key", "image_path", "alt_text", "caption", "is_active"];

    protected $casts = [
        "is_active" => "boolean",
    ];
}
