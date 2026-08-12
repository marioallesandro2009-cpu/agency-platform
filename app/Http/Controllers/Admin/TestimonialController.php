<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TestimonialController extends Controller
{
    public function index()
    {
        return Inertia::render("Admin/Testimonials/Index", [
            "testimonials" => Testimonial::ordered()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Testimonial::create($data);

        return back()->with("success", "Testimonial berhasil ditambahkan.");
    }

    public function update(Request $request, Testimonial $testimonial)
    {
        $data = $this->validated($request);
        $testimonial->update($data);

        return back()->with("success", "Testimonial berhasil diperbarui.");
    }

    public function destroy(Testimonial $testimonial)
    {
        $testimonial->delete();

        return back()->with("success", "Testimonial berhasil dihapus.");
    }

    public function togglePublish(Testimonial $testimonial)
    {
        $testimonial->update(["is_published" => ! $testimonial->is_published]);

        return back();
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            "name" => "required|string|max:255",
            "company" => "nullable|string|max:255",
            "position" => "nullable|string|max:255",
            "testimonial" => "required|string",
            "rating" => "required|integer|min:1|max:5",
            "order" => "nullable|integer",
        ]);
    }
}