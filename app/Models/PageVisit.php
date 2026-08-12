<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageVisit extends Model
{
    protected $fillable = [
        "path", "ip_address", "session_id", "user_agent", "referrer", "visit_date",
    ];
}