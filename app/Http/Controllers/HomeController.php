<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\PageSection;
use App\Models\PortfolioProject;
use App\Models\PricingPackage;
use App\Models\Service;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function index()
    {
        $pageSections = PageSection::forPage("home")->ordered()->get()->keyBy("section_type");

        $visibleTypes = $pageSections
            ->filter(fn ($section) => $section->is_visible)
            ->keys()
            ->values();

        $hero = null;
        if ($heroSection = $pageSections->get("hero")) {
            $hero = array_merge(
                ["title" => $heroSection->title, "subtitle" => $heroSection->subtitle, "image" => $heroSection->image],
                $heroSection->content ?? []
            );
        }

        $about = null;
        if ($aboutSection = $pageSections->get("about")) {
            $about = [
                "title" => $aboutSection->title,
                "image" => $aboutSection->image,
                "description" => $aboutSection->content["description"] ?? null,
                "button_text" => $aboutSection->content["button_text"] ?? null,
                "button_url" => $aboutSection->content["button_url"] ?? null,
                "extra_note" => $aboutSection->content["extra_note"] ?? null,
            ];
        }

        $whyUs = null;
        if ($whyUsSection = $pageSections->get("why_us")) {
            $whyUs = [
                "title" => $whyUsSection->title,
                "subtitle" => $whyUsSection->subtitle,
                "items" => $whyUsSection->content["items"] ?? [],
            ];
        }

        $process = null;
        if ($processSection = $pageSections->get("process")) {
            $process = [
                "title" => $processSection->title,
                "subtitle" => $processSection->subtitle,
                "steps" => $processSection->content["steps"] ?? [],
            ];
        }

        $cta = null;
        if ($ctaSection = $pageSections->get("cta")) {
            $cta = [
                "title" => $ctaSection->title,
                "subtitle" => $ctaSection->subtitle,
                "description" => $ctaSection->content["description"] ?? null,
                "cta_text" => $ctaSection->content["cta_text"] ?? null,
                "cta_url" => $ctaSection->content["cta_url"] ?? null,
            ];
        }

        $servicesSection = $pageSections->get("services");
        $pricingSection = $pageSections->get("pricing");
        $testimonialsSection = $pageSections->get("testimonials");
        $faqSection = $pageSections->get("faq");
        $portfolioSection = $pageSections->get("portfolio");
        $blogSection = $pageSections->get("blog");

        return view("pages.home", [
            "sections" => $visibleTypes,
            "hero" => $hero,
            "about" => $about,
            "servicesSection" => $this->sectionMeta($servicesSection),
            "services" => Service::published()->ordered()->get(),
            "whyUs" => $whyUs,
            "process" => $process,
            "pricingSection" => $this->sectionMeta($pricingSection),
            "pricingPackages" => PricingPackage::published()->ordered()->get(),
            "testimonialsSection" => $this->sectionMeta($testimonialsSection),
            "testimonials" => Testimonial::published()->ordered()->get(),
            "faqSection" => $this->sectionMeta($faqSection),
            "faqs" => Faq::published()->ordered()->get(),
            "ctaSection" => $cta,
            "portfolioSection" => $this->sectionMeta($portfolioSection),
            "portfolioProjects" => PortfolioProject::published()->ordered()->limit(3)->get([
                "id", "name", "slug", "category", "thumbnail", "short_description", "project_type", "live_url",
            ]),
            "blogSection" => $this->sectionMeta($blogSection),
            "latestPosts" => BlogPost::with("category")->published()->latestFirst()->take(3)->get(),
        ]);
    }

    private function sectionMeta(?PageSection $section): ?array
    {
        return $section ? ["title" => $section->title, "subtitle" => $section->subtitle] : null;
    }
}