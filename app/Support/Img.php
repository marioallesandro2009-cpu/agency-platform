<?php

namespace App\Support;

use App\Http\Controllers\ImageController;

/**
 * URL builders for the responsive WebP pipeline (see ImageController).
 * Only local uploads (/storage/...) are resized; anything else (external
 * URLs, placeholders) is passed through untouched.
 */
class Img
{
    /** Resized WebP URL for a /storage/... path, or the original URL. */
    public static function url(?string $src, int $width): ?string
    {
        if (! self::resizable($src)) {
            return $src;
        }

        $width = collect(ImageController::WIDTHS)->first(fn ($w) => $w >= $width) ?? max(ImageController::WIDTHS);

        return '/img/' . $width . '/' . ltrim(substr($src, strlen('/storage/')), '/') . '.webp';
    }

    /** srcset string covering the given widths (empty for non-local images). */
    public static function srcset(?string $src, array $widths = [480, 960, 1600]): string
    {
        if (! self::resizable($src)) {
            return '';
        }

        return collect($widths)->map(fn ($w) => self::url($src, $w) . " {$w}w")->implode(', ');
    }

    /** Local raster upload the WebP pipeline can handle (not SVG/GIF/external). */
    private static function resizable(?string $src): bool
    {
        return $src
            && str_starts_with($src, '/storage/')
            && in_array(strtolower(pathinfo($src, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true);
    }
}
