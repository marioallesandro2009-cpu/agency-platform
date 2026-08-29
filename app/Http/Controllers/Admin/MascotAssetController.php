<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MascotAsset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MascotAssetController extends Controller
{
    public function index()
    {
        return Inertia::render("Admin/MascotAssets/Index", [
            "assets" => MascotAsset::orderBy("id")->get(),
        ]);
    }

    // This slot's mascot is meant to render transparent, without its own
    // background box (see About.vue's Misi/Visi section) — restricted to
    // PNG (the only common format that supports alpha transparency well)
    // with a minimum size so it doesn't look stretched/blurry at display
    // size, unlike the other slots which accept any of jpg/png/webp.
    private const TRANSPARENT_PNG_SLOTS = ["misi_visi_tentang_kami"];

    public function update(Request $request, MascotAsset $mascotAsset)
    {
        $isTransparentSlot = in_array($mascotAsset->placement_key, self::TRANSPARENT_PNG_SLOTS, true);

        $validated = $request->validate([
            "image" => $isTransparentSlot
                ? "nullable|image|mimes:png|max:2048|dimensions:min_width=300,min_height=300"
                : "nullable|image|mimes:jpg,jpeg,png,webp|max:2048",
            "alt_text" => "nullable|string|max:255",
            "caption" => "nullable|string|max:255",
        ], [
            "image.mimes" => "Slot ini hanya menerima file PNG (supaya transparansinya terjaga).",
            "image.dimensions" => "Gambar terlalu kecil — minimal 300x300 piksel.",
        ]);

        $data = [
            "alt_text" => $validated["alt_text"] ?? null,
            "caption" => $validated["caption"] ?? null,
        ];

        if ($request->hasFile("image")) {
            if ($mascotAsset->image_path) {
                $oldPath = str_replace(Storage::disk("public")->url(""), "", $mascotAsset->image_path);
                Storage::disk("public")->delete($oldPath);
            }

            $path = $request->file("image")->store("mascots", "public");
            $data["image_path"] = Storage::disk("public")->url($path);
        }

        $mascotAsset->update($data);

        return back()->with("success", "Gambar maskot berhasil diperbarui.");
    }

    public function destroy(MascotAsset $mascotAsset)
    {
        if ($mascotAsset->image_path) {
            $oldPath = str_replace(Storage::disk("public")->url(""), "", $mascotAsset->image_path);
            Storage::disk("public")->delete($oldPath);
        }

        $mascotAsset->update([
            "image_path" => null,
            "alt_text" => null,
            "caption" => null,
        ]);

        return back()->with("success", "Gambar maskot berhasil dihapus.");
    }
}
