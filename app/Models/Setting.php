<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ["key", "value", "group"];

    public static function get(string $key, $default = null)
    {
        $settings = Cache::rememberForever("settings", function () {
            return self::all()->pluck("value", "key");
        });

        return $settings[$key] ?? $default;
    }

    public static function set(string $key, $value, string $group = "general"): void
    {
        self::updateOrCreate(["key" => $key], ["value" => $value, "group" => $group]);
        Cache::forget("settings");
        Cache::forget("settings.grouped");
    }

    public static function allGrouped(): array
    {
        return Cache::rememberForever("settings.grouped", function () {
            return self::all()->groupBy("group")->map(function ($items) {
                return $items->pluck("value", "key");
            })->toArray();
        });
    }
}