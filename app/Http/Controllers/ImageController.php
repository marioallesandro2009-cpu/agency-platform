<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

/**
 * On-demand responsive images: /img/{width}/{path}.webp resizes an uploaded
 * image from storage/app/public to the requested width as WebP, and writes
 * the result to public/img/{width}/{path}.webp — exactly the requested URL —
 * so every later request is served as a plain static file by the web server
 * and never reaches PHP again.
 *
 * Only whitelisted widths, only files inside storage/app/public, only
 * jpg/png/webp sources (GD, already available in this stack).
 */
class ImageController extends Controller
{
    public const WIDTHS = [320, 480, 640, 960, 1280, 1600];

    public function show(int $width, string $path)
    {
        abort_unless(in_array($width, self::WIDTHS, true), 404);
        abort_unless(str_ends_with($path, '.webp'), 404);

        $sourceRelative = substr($path, 0, -strlen('.webp'));
        $root = realpath(storage_path('app/public'));
        $source = realpath($root . DIRECTORY_SEPARATOR . $sourceRelative);

        // Path traversal guard: the resolved source must live under storage/app/public.
        abort_unless($source && str_starts_with($source, $root) && is_file($source), 404);

        $image = match (strtolower(pathinfo($source, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => @imagecreatefromjpeg($source),
            'png' => @imagecreatefrompng($source),
            'webp' => @imagecreatefromwebp($source),
            default => false,
        };
        abort_unless($image, 404);

        $srcW = imagesx($image);
        $srcH = imagesy($image);
        $targetW = min($width, $srcW);
        $targetH = (int) round($srcH * $targetW / $srcW);

        $resized = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $targetW, $targetH, $srcW, $srcH);

        $target = public_path("img/{$width}/{$path}");
        if (! is_dir(dirname($target))) {
            mkdir(dirname($target), 0755, true);
        }
        imagewebp($resized, $target, 78);
        imagedestroy($image);
        imagedestroy($resized);

        return new Response(file_get_contents($target), 200, [
            'Content-Type' => 'image/webp',
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }
}
