<?php

use App\Http\Controllers\Admin\AboutController as AdminAboutController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\MaintenancePlanController;
use App\Http\Controllers\Admin\MascotAssetController;
use App\Http\Controllers\Admin\PortfolioController as AdminPortfolioController;
use App\Http\Controllers\Admin\PricingController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\GeneralServiceController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\PricingController as PublicPricingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\FaqController as PublicFaqController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get("/sitemap.xml", [SitemapController::class, "index"])->name("sitemap");
Route::get("/robots.txt", [SitemapController::class, "robots"])->name("robots");

Route::middleware("track.visit")->group(function () {
    Route::get("/", [HomeController::class, "index"])->name("home");
    Route::get("/harga", [PublicPricingController::class, "index"])->name("pricing");
    Route::get("/jasa-website", [GeneralServiceController::class, "index"])->name("jasa-website");
    Route::get("/portfolio", [PortfolioController::class, "index"])->name("portfolio.index");
    Route::get("/portfolio/{portfolioProject}", [PortfolioController::class, "show"])->name("portfolio.show");
    Route::get("/jasa-website-umkm", [ServiceController::class, "show"])->name("service.umkm")->defaults("service", "jasa-website-umkm");
    Route::get("/jasa-website-company-profile", [ServiceController::class, "show"])->name("service.company-profile")->defaults("service", "jasa-website-company-profile");
    Route::get("/jasa-toko-online", [ServiceController::class, "show"])->name("service.toko-online")->defaults("service", "jasa-toko-online");
    Route::get("/jasa-web-development", [ServiceController::class, "show"])->name("service.web-development")->defaults("service", "jasa-web-development");
    Route::get("/tentang-kami", [AboutController::class, "index"])->name("about");
    Route::get("/faq", [PublicFaqController::class, "index"])->name("faq");
    Route::get("/blog", [App\Http\Controllers\BlogController::class, "index"])->name("blog.index");
    Route::get("/blog/category/{blogCategory}", [App\Http\Controllers\BlogController::class, "category"])->name("blog.category");
    Route::get("/blog/{blogPost}", [App\Http\Controllers\BlogController::class, "show"])->name("blog.show");

    Route::get("/konsultasi", [LeadController::class, "konsultasi"])->name("konsultasi");
    Route::get("/kontak", [LeadController::class, "kontak"])->name("kontak");
    Route::post("/leads", [LeadController::class, "store"])->middleware("throttle:5,1")->name("leads.store");
});

Route::get("/dashboard", function () {
    return \Inertia\Inertia::render("Dashboard");
})->middleware(["auth", "verified"])->name("dashboard");

Route::middleware("auth")->group(function () {
    Route::get("/profile", [ProfileController::class, "edit"])->name("profile.edit");
    Route::patch("/profile", [ProfileController::class, "update"])->name("profile.update");
    Route::delete("/profile", [ProfileController::class, "destroy"])->name("profile.destroy");
});

Route::middleware(["auth", "admin"])->prefix("admin")->name("admin.")->group(function () {
    Route::get("/", [DashboardController::class, "index"])->name("dashboard");
    Route::delete("/visitors/reset", [DashboardController::class, "resetVisitors"])->name("visitors.reset");
    Route::get("/settings", [SettingController::class, "index"])->name("settings.index");
    Route::put("/settings", [SettingController::class, "update"])->name("settings.update");

    Route::get("/about", [AdminAboutController::class, "edit"])->name("about.edit");
    Route::put("/about", [AdminAboutController::class, "update"])->name("about.update");

    Route::get("/page-sections", [App\Http\Controllers\Admin\PageSectionController::class, "index"])->name("page-sections.index");
    Route::patch("/page-sections/{pageSection}/toggle", [App\Http\Controllers\Admin\PageSectionController::class, "toggle"])->name("page-sections.toggle");
    Route::patch("/page-sections/{pageSection}", [App\Http\Controllers\Admin\PageSectionController::class, "update"])->name("page-sections.update");
    Route::post("/page-sections/reorder", [App\Http\Controllers\Admin\PageSectionController::class, "reorder"])->name("page-sections.reorder");
    Route::get("/portfolio", [AdminPortfolioController::class, "index"])->name("portfolio.index");
    Route::post("/portfolio", [AdminPortfolioController::class, "store"])->name("portfolio.store");
    Route::put("/portfolio/{portfolioProject}", [AdminPortfolioController::class, "update"])->name("portfolio.update");
    Route::delete("/portfolio/{portfolioProject}", [AdminPortfolioController::class, "destroy"])->name("portfolio.destroy");
    Route::patch("/portfolio/{portfolioProject}/toggle-publish", [AdminPortfolioController::class, "togglePublish"])->name("portfolio.toggle-publish");

    Route::get("/pricing", [PricingController::class, "index"])->name("pricing.index");
    Route::post("/pricing", [PricingController::class, "store"])->name("pricing.store");
    Route::put("/pricing/{pricingPackage}", [PricingController::class, "update"])->name("pricing.update");
    Route::delete("/pricing/{pricingPackage}", [PricingController::class, "destroy"])->name("pricing.destroy");
    Route::patch("/pricing/{pricingPackage}/toggle-publish", [PricingController::class, "togglePublish"])->name("pricing.toggle-publish");

    Route::get("/maintenance-plans", [MaintenancePlanController::class, "index"])->name("maintenance-plans.index");
    Route::post("/maintenance-plans", [MaintenancePlanController::class, "store"])->name("maintenance-plans.store");
    Route::put("/maintenance-plans/{maintenancePlan}", [MaintenancePlanController::class, "update"])->name("maintenance-plans.update");
    Route::delete("/maintenance-plans/{maintenancePlan}", [MaintenancePlanController::class, "destroy"])->name("maintenance-plans.destroy");
    Route::patch("/maintenance-plans/{maintenancePlan}/toggle-publish", [MaintenancePlanController::class, "togglePublish"])->name("maintenance-plans.toggle-publish");

    Route::get("/testimonials", [TestimonialController::class, "index"])->name("testimonials.index");
    Route::post("/testimonials", [TestimonialController::class, "store"])->name("testimonials.store");
    Route::put("/testimonials/{testimonial}", [TestimonialController::class, "update"])->name("testimonials.update");
    Route::delete("/testimonials/{testimonial}", [TestimonialController::class, "destroy"])->name("testimonials.destroy");
    Route::patch("/testimonials/{testimonial}/toggle-publish", [TestimonialController::class, "togglePublish"])->name("testimonials.toggle-publish");

    Route::get("/faqs", [FaqController::class, "index"])->name("faqs.index");
    Route::post("/faqs", [FaqController::class, "store"])->name("faqs.store");
    Route::put("/faqs/{faq}", [FaqController::class, "update"])->name("faqs.update");
    Route::delete("/faqs/{faq}", [FaqController::class, "destroy"])->name("faqs.destroy");
    Route::patch("/faqs/{faq}/toggle-publish", [FaqController::class, "togglePublish"])->name("faqs.toggle-publish");

    Route::get("/blog", [BlogPostController::class, "index"])->name("blog.index");
    Route::post("/blog", [BlogPostController::class, "store"])->name("blog.store");
    Route::put("/blog/{blogPost:id}", [BlogPostController::class, "update"])->name("blog.update");
    Route::delete("/blog/{blogPost:id}", [BlogPostController::class, "destroy"])->name("blog.destroy");
    Route::post("/blog-categories", [BlogCategoryController::class, "store"])->name("blog-categories.store");
    Route::delete("/blog-categories/{blogCategory:id}", [BlogCategoryController::class, "destroy"])->name("blog-categories.destroy");

    Route::get("/services", [App\Http\Controllers\Admin\ServiceController::class, "index"])->name("services.index");
    Route::post("/services", [App\Http\Controllers\Admin\ServiceController::class, "store"])->name("services.store");
    Route::put("/services/{service:id}", [App\Http\Controllers\Admin\ServiceController::class, "update"])->name("services.update");
    Route::delete("/services/{service:id}", [App\Http\Controllers\Admin\ServiceController::class, "destroy"])->name("services.destroy");
    Route::patch("/services/{service:id}/toggle-publish", [App\Http\Controllers\Admin\ServiceController::class, "togglePublish"])->name("services.toggle-publish");

    Route::get("/leads", [App\Http\Controllers\Admin\LeadController::class, "index"])->name("leads.index");
    Route::patch("/leads/{lead:id}", [App\Http\Controllers\Admin\LeadController::class, "updateStatus"])->name("leads.update-status");
    Route::delete("/leads/{lead:id}", [App\Http\Controllers\Admin\LeadController::class, "destroy"])->name("leads.destroy");

    Route::get("/maskot", [MascotAssetController::class, "index"])->name("mascot.index");
    Route::post("/maskot/{mascotAsset}", [MascotAssetController::class, "update"])->name("mascot.update");

    Route::get("/media", [App\Http\Controllers\Admin\MediaController::class, "index"])->name("media.index");
    Route::post("/media", [App\Http\Controllers\Admin\MediaController::class, "store"])->name("media.store");
    Route::post("/media/from-url", [App\Http\Controllers\Admin\MediaController::class, "storeFromUrl"])->name("media.store-from-url");
    Route::delete("/media/{media:id}", [App\Http\Controllers\Admin\MediaController::class, "destroy"])->name("media.destroy");

    Route::get("/navigation", [App\Http\Controllers\Admin\NavItemController::class, "index"])->name("navigation.index");
    Route::post("/navigation", [App\Http\Controllers\Admin\NavItemController::class, "store"])->name("navigation.store");
    Route::put("/navigation/{navItem:id}", [App\Http\Controllers\Admin\NavItemController::class, "update"])->name("navigation.update");
    Route::delete("/navigation/{navItem:id}", [App\Http\Controllers\Admin\NavItemController::class, "destroy"])->name("navigation.destroy");
    Route::patch("/navigation/{navItem:id}/toggle", [App\Http\Controllers\Admin\NavItemController::class, "toggle"])->name("navigation.toggle");
    Route::post("/navigation/reorder", [App\Http\Controllers\Admin\NavItemController::class, "reorder"])->name("navigation.reorder");
});

require __DIR__."/auth.php";