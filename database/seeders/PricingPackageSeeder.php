<?php

namespace Database\Seeders;

use App\Models\PricingPackage;
use Illuminate\Database\Seeder;

class PricingPackageSeeder extends Seeder
{
    public function run(): void
    {
        // Full taxonomy: 4 categories x 3 tiers (Seed/Growth/Scale) = 12
        // packages. Tier names are identical across categories — only the
        // tagline, price, and feature checklist vary per category.
        $categories = [
            "landing_page" => [
                "label" => "Landing Page",
                "tiers" => [
                    "seed" => [
                        "tagline" => "Mulai promosi pertama",
                        "price" => 750000,
                        "features" => ["1 Section Utama", "Domain + Hosting 1 Tahun", "Responsive", "WhatsApp CTA", "Revisi 2x"],
                    ],
                    "growth" => [
                        "tagline" => "Kembangkan konversi",
                        "price" => 1200000,
                        "features" => ["3-4 Section", "Domain + Hosting 1 Tahun", "Responsive", "WhatsApp CTA", "Analytics & Pixel", "Revisi 4x"],
                    ],
                    "scale" => [
                        "tagline" => "Maksimalkan hasil campaign",
                        "price" => 2000000,
                        "features" => ["5+ Section", "Domain + Hosting 1 Tahun", "Responsive", "Animasi Custom", "Live Chat Widget", "A/B Testing Ready", "Revisi 7x"],
                    ],
                ],
            ],
            "company_profile" => [
                "label" => "Company Profile",
                "tiers" => [
                    "seed" => [
                        "tagline" => "Mulai bangun kredibilitas",
                        "price" => 1750000,
                        "features" => ["5 Halaman", "Domain + Hosting 1 Tahun", "SEO Friendly", "Desain Custom", "Revisi 3x", "Maintenance Gratis 1 Bulan"],
                    ],
                    "growth" => [
                        "tagline" => "Kembangkan kehadiran digital",
                        "price" => 2750000,
                        "features" => ["Halaman Unlimited", "Domain + Hosting 1 Tahun", "SEO Friendly", "Desain Custom", "Blog/CMS", "Multi Language (Google Translate)", "Revisi 5x", "Maintenance Gratis 2 Bulan"],
                    ],
                    "scale" => [
                        "tagline" => "Perkuat posisi brand",
                        "price" => 4400000,
                        "features" => ["Halaman Unlimited", "Domain + Hosting 1 Tahun", "SEO Friendly", "Desain Custom", "Blog/CMS", "Multi Language (Manual Translate)", "Revisi 7x", "Maintenance Gratis 3 Bulan"],
                    ],
                ],
            ],
            "ecommerce" => [
                "label" => "E-commerce",
                "tiers" => [
                    "seed" => [
                        "tagline" => "Mulai jualan online",
                        "price" => 2000000,
                        "features" => ["Produk hingga 30 Item", "Payment Gateway", "Dashboard Laporan Basic"],
                    ],
                    "growth" => [
                        "tagline" => "Kembangkan basis pelanggan",
                        "price" => 3400000,
                        "features" => ["Produk hingga 100 Item", "Payment Gateway", "Cek Ongkir Otomatis", "Analitik Penjualan"],
                    ],
                    "scale" => [
                        "tagline" => "Perluas skala bisnis",
                        "price" => 6300000,
                        "features" => ["Produk Unlimited", "Payment Gateway", "Cek Ongkir Otomatis", "Dashboard Insight & Rekomendasi"],
                    ],
                ],
            ],
            "sistem_informasi" => [
                "label" => "Sistem Informasi",
                "tiers" => [
                    "seed" => [
                        "tagline" => "Mulai digitalisasi sederhana",
                        "price" => 2500000,
                        "features" => ["CRUD Sederhana", "1 Role Pengguna"],
                    ],
                    "growth" => [
                        "tagline" => "Kembangkan sistem operasional",
                        "price" => 5000000,
                        "features" => ["Multi-Role", "2-3 Modul", "Dashboard Statistik", "Notifikasi WA/Email"],
                    ],
                    "scale" => [
                        "tagline" => "Integrasikan seluruh proses bisnis",
                        "price" => 9500000,
                        "features" => ["Modul Lengkap", "Integrasi API", "Dashboard Insight"],
                    ],
                ],
            ],
        ];

        $renewalPrices = ["seed" => 400000, "growth" => 500000, "scale" => 600000];

        // Old rows are replaced entirely by the taxonomy above — clear them
        // out first so no stale/mismatched rows linger.
        PricingPackage::query()->delete();

        $order = 1;
        foreach ($categories as $categoryValue => $category) {
            foreach ($category["tiers"] as $tierValue => $tier) {
                $tierLabel = ucfirst($tierValue);

                PricingPackage::updateOrCreate(
                    ["slug" => "{$categoryValue}-{$tierValue}"],
                    [
                        "name" => "{$category['label']} - {$tierLabel}",
                        "category" => $categoryValue,
                        "tier" => $tierValue,
                        "tagline" => $tier["tagline"],
                        "description" => null,
                        "image" => null,
                        "price" => $tier["price"],
                        "price_prefix" => null,
                        "price_suffix" => null,
                        "renewal_price" => $renewalPrices[$tierValue],
                        "features" => $tier["features"],
                        "badge" => $tierValue === "growth" ? "Best Value" : null,
                        "is_recommended" => $tierValue === "growth",
                        "cta_text" => "Pesan Website",
                        "cta_url" => "/konsultasi",
                        "order" => $order++,
                        "is_published" => true,
                    ]
                );
            }
        }
    }
}
