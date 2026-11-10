<?php

namespace App\Http\Controllers;

use App\Mail\NewLeadNotification;
use App\Models\Lead;
use App\Models\PricingPackage;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LeadController extends Controller
{
    public function konsultasi(Request $request)
    {
        $serviceInterested = $request->query("service");
        $selectedPackage = null;

        $category = $request->query("kategori");
        $tier = $request->query("tier");

        if ($category && $tier) {
            $package = PricingPackage::where("category", $category)
                ->where("tier", $tier)
                ->published()
                ->first();

            if ($package) {
                $serviceInterested = $package->name;
                $selectedPackage = [
                    "category" => $package->category,
                    "tier" => $package->tier,
                    "name" => $package->name,
                    "price" => $package->price,
                    "price_prefix" => $package->price_prefix,
                    "price_suffix" => $package->price_suffix,
                    "features" => $package->features,
                ];
            }
        }

        return view("pages.lead-form", [
            "context" => "konsultasi",
            "title" => "Konsultasi Gratis",
            "subtitle" => "Ceritakan kebutuhan website atau software Anda, tim kami akan segera menghubungi lewat WhatsApp.",
            "serviceInterested" => $serviceInterested,
            "selectedPackage" => $selectedPackage,
        ]);
    }

    public function kontak()
    {
        return view("pages.lead-form", [
            "context" => "kontak",
            "title" => "Hubungi Kami",
            "subtitle" => "Ada pertanyaan? Kirim pesan dan kami akan segera membalas lewat WhatsApp.",
            "serviceInterested" => null,
            "selectedPackage" => null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            "name" => "required|string|max:255",
            "whatsapp" => ["required", "string", "max:30", "regex:/^[0-9+\-\s()]{8,30}$/"],
            "email" => "nullable|email|max:255",
            "address" => "nullable|string|max:1000",
            "message" => "nullable|string|max:1000",
            "service_interested" => "nullable|string|max:255",
            "budget_estimate" => "nullable|string|max:100",
            "pricing_category" => "nullable|string|max:100",
            "pricing_tier" => "nullable|string|max:100",
            "contact_position" => "nullable|string|max:255",
            "utm_source" => "nullable|string|max:100",
            "utm_medium" => "nullable|string|max:100",
            "utm_campaign" => "nullable|string|max:100",
            "referrer" => "nullable|string|max:500",
            "landing_page" => "nullable|string|max:500",
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

        $notificationEmail = Setting::get("admin_notification_email");
        if ($notificationEmail) {
            Mail::to($notificationEmail)->queue(new NewLeadNotification($lead));
        }

        // Lead-based business model: the team reaches out, the visitor isn't
        // auto-redirected into a WhatsApp chat. See project decision — this
        // replaced the previous Inertia::location() WA handoff.
        $message = "Terima kasih! Tim kami akan menghubungi Anda dalam 1x24 jam via WhatsApp.";

        // The Blade forms submit via fetch (blade-app.js): answer with JSON
        // instead of redirecting into a full page render nobody displays.
        if ($request->expectsJson()) {
            return response()->json(["message" => $message]);
        }

        return back()->with("success", $message);
    }
}
