<?php

namespace App\Http\Controllers;

use App\Models\Faq;

class FaqController extends Controller
{
    public function index()
    {
        $faqs = Faq::published()->ordered()->get();

        return view("pages.faq", [
            "faqs" => $faqs,
            "categories" => $faqs->pluck("category")->filter()->unique()->values(),
        ]);
    }
}