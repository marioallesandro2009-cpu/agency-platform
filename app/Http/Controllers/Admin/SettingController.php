<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        return Inertia::render("Admin/Settings/Index", [
            "settings" => Setting::allGrouped(),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            "group" => "required|string",
            "values" => "required|array",
        ]);

        foreach ($data["values"] as $key => $value) {
            Setting::set($key, $value, $data["group"]);
        }

        return back()->with("success", "Pengaturan berhasil disimpan.");
    }
}