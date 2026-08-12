<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Inertia\Inertia;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::published()->ordered()->get();

        return Inertia::render("Public/Faq", [
            "faqs" => $faqs,
            "categories" => $faqs->pluck("category")->filter()->unique()->values(),
        ]);
    }
}