<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        "name", "whatsapp", "email", "message", "service_interested",
        "source", "source_url", "status", "admin_notes",
    ];

    public function scopeStatus($query, string $status)
    {
        return $query->where("status", $status);
    }

    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc("created_at");
    }
}
