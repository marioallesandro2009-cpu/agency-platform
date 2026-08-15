<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        "name", "whatsapp", "email", "address", "message", "service_interested",
        "budget_estimate", "pricing_category", "pricing_tier", "source", "source_url", "status", "admin_notes",
    ];

    public function scopeStatus($query, string $status)
    {
        return $query->where("status", $status);
    }

    public function scopeLatestFirst($query)
    {
        return $query->orderByDesc("created_at");
    }

    /**
     * Masks a name for public display (live-ticker style): keeps the first
     * two characters and the last one, replaces everything between with a
     * fixed "***" regardless of the original length — e.g. "Budiman" ->
     * "Bu***n". Short names (<=3 chars) fall back to first-char + dots.
     */
    public static function maskName(string $name): string
    {
        $name = trim($name);
        $length = mb_strlen($name);

        if ($length <= 3) {
            return mb_substr($name, 0, 1).str_repeat("*", max($length - 1, 1));
        }

        return mb_substr($name, 0, 2)."***".mb_substr($name, -1);
    }

    /**
     * Recent leads formatted for the public live-activity ticker: masked
     * name + the service they expressed interest in, newest first. Returns
     * an empty array (component stays hidden) unless there are at least
     * $minCount qualifying leads — a ticker with 1-2 entries reads as fake
     * rather than reassuring, and leads with no service_interested value
     * don't have anything meaningful to show.
     */
    public static function recentTickerActivity(int $limit = 8, int $minCount = 3): array
    {
        $leads = static::latestFirst()
            ->whereNotNull("service_interested")
            ->where("service_interested", "!=", "")
            ->take($limit)
            ->get(["name", "service_interested"]);

        if ($leads->count() < $minCount) {
            return [];
        }

        return $leads->map(fn ($lead) => [
            "name" => static::maskName($lead->name),
            "service" => $lead->service_interested,
        ])->all();
    }
}
