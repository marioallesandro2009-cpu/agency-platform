<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class MediaController extends Controller
{
    /**
     * Domains known to license their images free for commercial use
     * (Unsplash/Pexels/Pixabay/Burst/StockSnap). Anything else is rejected —
     * there's no reliable way to verify an arbitrary link's license terms,
     * so we only trust a short allowlist instead of guessing.
     */
    private const ALLOWED_IMAGE_HOSTS = [
        "unsplash.com", "images.unsplash.com", "plus.unsplash.com",
        "pexels.com", "images.pexels.com",
        "pixabay.com", "cdn.pixabay.com", "pixabay.com",
        "burst.shopify.com",
        "stocksnap.io", "cdn.stocksnap.io",
    ];

    private function isAllowedHost(string $host): bool
    {
        $host = strtolower($host);

        foreach (self::ALLOWED_IMAGE_HOSTS as $allowed) {
            if ($host === $allowed || Str::endsWith($host, ".{$allowed}")) {
                return true;
            }
        }

        return false;
    }

    public function index(Request $request)
    {
        $media = Media::latest()->get();

        if ($request->wantsJson()) {
            return response()->json($media);
        }

        return Inertia::render("Admin/Media/Index", [
            "media" => $media,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            "file" => "required|image|mimes:jpeg,png,jpg,gif,webp|max:4096",
        ]);

        $file = $request->file("file");
        $path = $file->store("media", "public");

        $media = Media::create([
            "name" => $file->getClientOriginalName(),
            "path" => $path,
            "disk" => "public",
            "mime_type" => $file->getMimeType(),
            "size" => $file->getSize(),
            "uploaded_by" => $request->user()->id,
        ]);

        if ($request->wantsJson()) {
            return response()->json($media);
        }

        return back()->with("success", "Gambar berhasil diunggah.");
    }

    public function storeFromUrl(Request $request)
    {
        $data = $request->validate([
            "url" => ["required", "url", "max:2048", "starts_with:https://"],
        ]);

        $host = parse_url($data["url"], PHP_URL_HOST) ?: "";

        if (!$this->isAllowedHost($host)) {
            throw ValidationException::withMessages([
                "url" => "Link ditolak: sumber ini bukan situs stock foto gratis-komersial yang terverifikasi (Unsplash, Pexels, Pixabay, Burst, StockSnap). Untuk gambar dari sumber lain, unggah filenya secara manual setelah memastikan Anda punya lisensi commercial use-nya.",
            ]);
        }

        try {
            $response = Http::timeout(10)->withOptions(["allow_redirects" => ["max" => 3]])->get($data["url"]);
        } catch (\Throwable $e) {
            throw ValidationException::withMessages(["url" => "Gagal mengambil gambar dari link tersebut."]);
        }

        if (!$response->successful()) {
            throw ValidationException::withMessages(["url" => "Gagal mengambil gambar dari link tersebut."]);
        }

        $contentType = $response->header("Content-Type");
        $allowedMimes = ["image/jpeg", "image/png", "image/gif", "image/webp"];
        $mime = strtolower(explode(";", (string) $contentType)[0]);

        if (!in_array($mime, $allowedMimes, true)) {
            throw ValidationException::withMessages(["url" => "Link tersebut bukan file gambar (jpg/png/gif/webp)."]);
        }

        $body = $response->body();
        if (strlen($body) > 8 * 1024 * 1024) {
            throw ValidationException::withMessages(["url" => "Ukuran gambar terlalu besar (maks 8MB)."]);
        }

        $extension = match ($mime) {
            "image/jpeg" => "jpg",
            "image/png" => "png",
            "image/gif" => "gif",
            "image/webp" => "webp",
        };

        $filename = Str::uuid().".".$extension;
        $path = "media/{$filename}";
        Storage::disk("public")->put($path, $body);

        $media = Media::create([
            "name" => basename(parse_url($data["url"], PHP_URL_PATH) ?: $filename),
            "path" => $path,
            "disk" => "public",
            "mime_type" => $mime,
            "size" => strlen($body),
            "uploaded_by" => $request->user()->id,
            "source_url" => $data["url"],
        ]);

        if ($request->wantsJson()) {
            return response()->json($media);
        }

        return back()->with("success", "Gambar dari link berhasil ditambahkan.");
    }

    public function destroy(Media $media)
    {
        Storage::disk($media->disk)->delete($media->path);
        $media->delete();

        return back()->with("success", "Gambar berhasil dihapus.");
    }
}
