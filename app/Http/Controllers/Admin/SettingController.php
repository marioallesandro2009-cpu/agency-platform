<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class SettingController extends Controller
{
    /**
     * Closed whitelist of editable settings, keyed by group. `Setting::allGrouped()`
     * is broadcast to every public page via HandleInertiaRequests — a settings
     * write endpoint that accepted an arbitrary group/key pair would let a future
     * (or compromised) staff account silently add a value that gets leaked to
     * every visitor. Keep this in sync with SettingSeeder and Admin/Settings/Index.vue.
     *
     * "favicon" is deliberately absent — it goes through updateFavicon() below,
     * which needs file validation (square, min dimensions, png/ico mime) that
     * this generic string-values endpoint can't express.
     */
    private const EDITABLE_KEYS = [
        "general" => ["website_name", "tagline", "description", "logo", "logo_dark", "og_image", "footer_disclaimer"],
        "contact" => ["whatsapp_number", "email", "phone", "address", "maps_url"],
        "social" => ["instagram", "tiktok", "facebook", "linkedin", "youtube"],
        "branding" => ["primary_color", "secondary_color"],
        "integrations" => ["ga_measurement_id", "meta_pixel_id"],
        "notifications" => ["admin_notification_email"],
    ];

    public function index()
    {
        return Inertia::render("Admin/Settings/Index", [
            "settings" => Setting::allGrouped(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            "group" => ["required", "string", Rule::in(array_keys(self::EDITABLE_KEYS))],
            "values" => "required|array",
        ]);

        $allowedKeys = self::EDITABLE_KEYS[$data["group"]];

        foreach ($data["values"] as $key => $value) {
            if (!in_array($key, $allowedKeys, true)) {
                continue;
            }

            Setting::set($key, is_string($value) ? trim($value) : $value, $data["group"]);
        }

        return back()->with("success", "Pengaturan berhasil disimpan.");
    }

    /**
     * Favicon needs its own endpoint (rather than going through update()
     * above) because it needs file validation that a generic string-values
     * PUT can't express: real square-image dimension checks. getimagesize()
     * reads dimensions without needing GD/Imagick, so this works even though
     * this project has no image processing library installed.
     *
     * The 180px floor covers both Google's favicon minimum (48px, ideally a
     * multiple of 48) and Apple's apple-touch-icon recommendation (180x180)
     * with a single uploaded file — no server-side resizing needed.
     */
    public function updateFavicon(Request $request)
    {
        $request->validate([
            "favicon" => ["required", "file", "max:2048", "mimes:png,ico"],
        ]);

        $file = $request->file("favicon");

        $dimensions = @getimagesize($file->getRealPath());
        if (!$dimensions || $dimensions[0] !== $dimensions[1] || $dimensions[0] < 180) {
            throw ValidationException::withMessages([
                "favicon" => "Favicon harus berupa gambar persegi (lebar = tinggi), minimal 180x180px, supaya tajam di tab browser, hasil pencarian Google, dan ikon iOS.",
            ]);
        }

        $this->deleteFaviconFile(Setting::get("favicon"));

        $path = $file->store("favicon", "public");
        Setting::set("favicon", Storage::disk("public")->url($path), "general");

        return back()->with("success", "Favicon berhasil diperbarui.");
    }

    public function destroyFavicon()
    {
        $this->deleteFaviconFile(Setting::get("favicon"));

        Setting::set("favicon", null, "general");

        return back()->with("success", "Favicon kembali ke default bawaan.");
    }

    private function deleteFaviconFile(?string $url): void
    {
        if (!$url) {
            return;
        }

        $path = str_replace(Storage::disk("public")->url(""), "", $url);
        Storage::disk("public")->delete($path);
    }
}
