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

    public function update(Request $request, MascotAsset $mascotAsset)
    {
        $validated = $request->validate([
            "image" => "nullable|image|mimes:jpg,jpeg,png,webp|max:2048",
            "alt_text" => "nullable|string|max:255",
            "caption" => "nullable|string|max:255",
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
}
