<?php

namespace App\Support;

use App\Http\Controllers\ImageController;

/**
 * URL builders for the responsive WebP pipeline (see ImageController).
 * Only local uploads (/storage/...) are resized; anything else (external
 * URLs, placeholders) is passed through untouched.
 *
 * Images under /images/blog/ ship with the repo (storage/ is not deployed by
 * git) and already have `name-480|960|1280.webp` next to the `name.jpg` kept
 * for social previews, so those are mapped instead of being resized on demand.
 */
class Img
{
    private const PRERENDERED_DIR = '/images/blog/';

    private const PRERENDERED_WIDTHS = [480, 960, 1280];

    /** Resized WebP URL for a /storage/... path, or the original URL. */
    public static function url(?string $src, int $width): ?string
    {
        if (self::prerendered($src)) {
            $width = collect(self::PRERENDERED_WIDTHS)->first(fn ($w) => $w >= $width) ?? max(self::PRERENDERED_WIDTHS);

            return substr($src, 0, -strlen('.jpg')) . "-{$width}.webp";
        }

        if (! self::resizable($src)) {
            return $src;
        }

        $width = collect(ImageController::WIDTHS)->first(fn ($w) => $w >= $width) ?? max(ImageController::WIDTHS);

        return '/img/' . $width . '/' . ltrim(substr($src, strlen('/storage/')), '/') . '.webp';
    }

    /** srcset string covering the given widths (empty for non-local images). */
    public static function srcset(?string $src, array $widths = [480, 960, 1600]): string
    {
        if (self::prerendered($src)) {
            return collect(self::PRERENDERED_WIDTHS)->map(fn ($w) => self::url($src, $w) . " {$w}w")->implode(', ');
        }

        if (! self::resizable($src)) {
            return '';
        }

        return collect($widths)->map(fn ($w) => self::url($src, $w) . " {$w}w")->implode(', ');
    }

    private static function prerendered(?string $src): bool
    {
        return $src && str_starts_with($src, self::PRERENDERED_DIR) && str_ends_with($src, '.jpg');
    }

    /** Local raster upload the WebP pipeline can handle (not SVG/GIF/external). */
    private static function resizable(?string $src): bool
    {
        return $src
            && str_starts_with($src, '/storage/')
            && in_array(strtolower(pathinfo($src, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png', 'webp'], true);
    }
}
