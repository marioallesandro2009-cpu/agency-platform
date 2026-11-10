<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        "name", "whatsapp", "email", "address", "message", "service_interested",
        "budget_estimate", "pricing_category", "pricing_tier", "source", "source_url", "status", "admin_notes",
        "contact_position", "utm_source", "utm_medium", "utm_campaign", "referrer", "landing_page",
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
