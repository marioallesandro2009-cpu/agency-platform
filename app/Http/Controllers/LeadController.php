<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeadController extends Controller
{
    public function konsultasi(Request $request)
    {
        return Inertia::render("Public/LeadForm", [
            "context" => "konsultasi",
            "title" => "Konsultasi Gratis",
            "subtitle" => "Ceritakan kebutuhan website Anda, tim kami akan segera menghubungi lewat WhatsApp.",
            "serviceInterested" => $request->query("service"),
        ]);
    }

    public function kontak()
    {
        return Inertia::render("Public/LeadForm", [
            "context" => "kontak",
            "title" => "Hubungi Kami",
            "subtitle" => "Ada pertanyaan? Kirim pesan dan kami akan segera membalas lewat WhatsApp.",
            "serviceInterested" => null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            "name" => "required|string|max:255",
            "whatsapp" => ["required", "string", "max:30", "regex:/^[0-9+\-\s()]{8,30}$/"],
            "email" => "nullable|email|max:255",
            "message" => "nullable|string|max:1000",
            "service_interested" => "nullable|string|max:255",
            // "cta_home" — the inline "Talk to Us"-style form embedded in the
            // homepage's Final CTA section (ContactCtaForm.vue), distinct from
            // the dedicated /konsultasi and /kontak pages.
            "source" => "required|in:konsultasi,kontak,cta_home",
        ]);

        $lead = Lead::create([
            ...$data,
            "source_url" => $request->headers->get("referer"),
            "status" => "new",
        ]);

        $whatsappNumber = Setting::allGrouped()["contact"]["whatsapp_number"] ?? null;

        if (! $whatsappNumber) {
            return back()->with("success", "Terima kasih, permintaan Anda sudah kami terima.");
        }

        $text = "Halo, saya {$lead->name}.";
        if ($lead->service_interested) {
            $text .= " Saya tertarik dengan layanan {$lead->service_interested}.";
        }
        if ($lead->message) {
            $text .= " {$lead->message}";
        }

        $waUrl = "https://wa.me/{$whatsappNumber}?text=".rawurlencode($text);

        return Inertia::location($waUrl);
    }
}
