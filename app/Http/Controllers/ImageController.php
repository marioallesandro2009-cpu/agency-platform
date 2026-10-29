<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Throwable;

/**
 * On-demand responsive images: /img/{width}/{path}.webp resizes an uploaded
 * image from storage/app/public to the requested width as WebP, and writes
 * the result to public/img/{width}/{path}.webp — exactly the requested URL —
 * so every later request is served as a plain static file by the web server
 * and never reaches PHP again.
 *
 * Only whitelisted widths, only files inside the public upload folder, only
 * jpg/png/webp sources (GD). Whenever a WebP cannot be produced (GD without
 * WebP, an oversized upload, an unwritable public/img, uploads living in
 * public/storage instead of storage/app/public on shared hosting) the
 * request falls back to the original /storage/... file, so an image is
 * never lost — it is just served unoptimised.
 */
class ImageController extends Controller
{
    public const WIDTHS = [320, 480, 640, 960, 1280, 1600];

    public function show(int $width, string $path)
    {
        abort_unless(in_array($width, self::WIDTHS, true), 404);
        abort_unless(str_ends_with($path, '.webp'), 404);

        $sourceRelative = substr($path, 0, -strlen('.webp'));
        abort_if(str_contains($sourceRelative, '..'), 404);

        $original = '/storage/' . ltrim($sourceRelative, '/');
        $source = $this->locate($sourceRelative);
        if (! $source) {
            return redirect($original);
        }

        try {
            $webp = $this->resize($source, $width);
        } catch (Throwable $e) {
            report($e);
            $webp = null;
        }
        if ($webp === null) {
            return redirect($original);
        }

        $target = public_path("img/{$width}/{$path}");
        if (! is_dir(dirname($target))) {
            @mkdir(dirname($target), 0755, true);
        }
        @file_put_contents($target, $webp);

        return new Response($webp, 200, [
            'Content-Type' => 'image/webp',
            'Cache-Control' => 'public, max-age=31536000, immutable',
        ]);
    }

    /** Absolute path of the upload, wherever this host keeps the public disk. */
    private function locate(string $relative): ?string
    {
        foreach ([storage_path('app/public'), public_path('storage')] as $root) {
            $root = realpath($root);
            $source = $root ? realpath($root . DIRECTORY_SEPARATOR . $relative) : false;

            // Path traversal guard: the resolved source must live under the root.
            if ($source && str_starts_with($source, $root) && is_file($source)) {
                return $source;
            }
        }

        return null;
    }

    /** WebP bytes of $source scaled down to $width, or null if GD can't. */
    private function resize(string $source, int $width): ?string
    {
        if (! function_exists('imagewebp')) {
            return null;
        }

        // Phone-camera uploads decode to ~100MB of pixels; give GD room.
        @ini_set('memory_limit', '512M');

        $image = match (strtolower(pathinfo($source, PATHINFO_EXTENSION))) {
            'jpg', 'jpeg' => function_exists('imagecreatefromjpeg') ? @imagecreatefromjpeg($source) : false,
            'png' => function_exists('imagecreatefrompng') ? @imagecreatefrompng($source) : false,
            'webp' => function_exists('imagecreatefromwebp') ? @imagecreatefromwebp($source) : false,
            default => false,
        };
        if (! $image) {
            return null;
        }

        $srcW = imagesx($image);
        $srcH = imagesy($image);
        $targetW = min($width, $srcW);
        $targetH = (int) round($srcH * $targetW / $srcW);

        $resized = imagecreatetruecolor($targetW, $targetH);
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
        imagecopyresampled($resized, $image, 0, 0, 0, 0, $targetW, $targetH, $srcW, $srcH);

        ob_start();
        $ok = imagewebp($resized, null, 78);
        $bytes = ob_get_clean();
        imagedestroy($image);
        imagedestroy($resized);

        return $ok && $bytes !== '' ? $bytes : null;
    }
}
