<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FaqController extends Controller
{
    public function index()
    {
        return Inertia::render("Admin/Faqs/Index", [
            "faqs" => Faq::ordered()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        Faq::create($data);

        return back()->with("success", "FAQ berhasil ditambahkan.");
    }

    public function update(Request $request, Faq $faq)
    {
        $data = $this->validated($request);
        $faq->update($data);

        return back()->with("success", "FAQ berhasil diperbarui.");
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return back()->with("success", "FAQ berhasil dihapus.");
    }

    public function togglePublish(Faq $faq)
    {
        $faq->update(["is_published" => ! $faq->is_published]);

        return back();
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            "question" => "required|string|max:500",
            "answer" => "required|string",
            "category" => "nullable|string|max:100",
            "order" => "nullable|integer",
        ]);
    }
}