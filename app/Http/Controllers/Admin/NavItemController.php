<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NavItemController extends Controller
{
    public function index()
    {
        return Inertia::render("Admin/Navigation/Index", [
            "navItems" => NavItem::ordered()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data["order"] = NavItem::max("order") + 1;

        NavItem::create($data);

        return back()->with("success", "Menu berhasil ditambahkan.");
    }

    public function update(Request $request, NavItem $navItem)
    {
        $navItem->update($this->validated($request));

        return back()->with("success", "Menu berhasil diperbarui.");
    }

    public function destroy(NavItem $navItem)
    {
        $navItem->delete();

        return back()->with("success", "Menu berhasil dihapus.");
    }

    public function toggle(NavItem $navItem)
    {
        $navItem->update(["is_visible" => ! $navItem->is_visible]);

        return back();
    }

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            "order" => "required|array",
            "order.*" => "required|integer|exists:nav_items,id",
        ]);

        foreach ($validated["order"] as $index => $id) {
            NavItem::where("id", $id)->update(["order" => $index + 1]);
        }

        return back();
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            "label" => "required|string|max:100",
            "url" => "required|string|max:255",
            "is_visible" => "boolean",
        ]);
    }
}
