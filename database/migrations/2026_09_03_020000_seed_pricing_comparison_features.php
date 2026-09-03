<?php

use App\Models\PricingPackage;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Populates the per-tier feature comparison table shown on /harga
     * (row = feature, column = Seed/Growth/Scale) for each of the 4
     * existing categories. Keyed by category+tier so it matches the same
     * rows across all 3 tiers of a category and the table can align them.
     */
    public function up(): void
    {
        $rows = [
            'landing_page' => [
                'seed' => [
                    ['feature' => 'SSL Certificate', 'value' => 'Gratis'],
                    ['feature' => 'Domain & Hosting', 'value' => '1 Tahun'],
                    ['feature' => 'Jumlah Section', 'value' => '1 Section Utama'],
                    ['feature' => 'Email Bisnis', 'value' => 'Gratis'],
                    ['feature' => 'Revisi Desain', 'value' => '2x'],
                    ['feature' => 'Maintenance Gratis', 'value' => '-'],
                ],
                'growth' => [
                    ['feature' => 'SSL Certificate', 'value' => 'Gratis'],
                    ['feature' => 'Domain & Hosting', 'value' => '1 Tahun'],
                    ['feature' => 'Jumlah Section', 'value' => '3-4 Section'],
                    ['feature' => 'Email Bisnis', 'value' => 'Gratis'],
                    ['feature' => 'Revisi Desain', 'value' => '4x'],
                    ['feature' => 'Maintenance Gratis', 'value' => '1 Bulan'],
                ],
                'scale' => [
                    ['feature' => 'SSL Certificate', 'value' => 'Gratis'],
                    ['feature' => 'Domain & Hosting', 'value' => '1 Tahun'],
                    ['feature' => 'Jumlah Section', 'value' => '5+ Section + Animasi'],
                    ['feature' => 'Email Bisnis', 'value' => 'Gratis'],
                    ['feature' => 'Revisi Desain', 'value' => '7x'],
                    ['feature' => 'Maintenance Gratis', 'value' => '2 Bulan'],
                ],
            ],
            'company_profile' => [
                'seed' => [
                    ['feature' => 'SSL Certificate', 'value' => 'Gratis'],
                    ['feature' => 'Domain & Hosting', 'value' => '1 Tahun'],
                    ['feature' => 'Jumlah Halaman', 'value' => '5 Halaman'],
                    ['feature' => 'Email Bisnis', 'value' => 'Gratis'],
                    ['feature' => 'Blog/CMS', 'value' => '-'],
                    ['feature' => 'Revisi Desain', 'value' => '3x'],
                    ['feature' => 'Maintenance Gratis', 'value' => '1 Bulan'],
                ],
                'growth' => [
                    ['feature' => 'SSL Certificate', 'value' => 'Gratis'],
                    ['feature' => 'Domain & Hosting', 'value' => '1 Tahun'],
                    ['feature' => 'Jumlah Halaman', 'value' => 'Unlimited'],
                    ['feature' => 'Email Bisnis', 'value' => 'Gratis'],
                    ['feature' => 'Blog/CMS', 'value' => 'Ya'],
                    ['feature' => 'Revisi Desain', 'value' => '5x'],
                    ['feature' => 'Maintenance Gratis', 'value' => '2 Bulan'],
                ],
                'scale' => [
                    ['feature' => 'SSL Certificate', 'value' => 'Gratis'],
                    ['feature' => 'Domain & Hosting', 'value' => '1 Tahun'],
                    ['feature' => 'Jumlah Halaman', 'value' => 'Unlimited'],
                    ['feature' => 'Email Bisnis', 'value' => 'Gratis'],
                    ['feature' => 'Blog/CMS', 'value' => 'Ya + Multi Language'],
                    ['feature' => 'Revisi Desain', 'value' => '7x'],
                    ['feature' => 'Maintenance Gratis', 'value' => '3 Bulan'],
                ],
            ],
            'ecommerce' => [
                'seed' => [
                    ['feature' => 'SSL Certificate', 'value' => 'Gratis'],
                    ['feature' => 'Domain & Hosting', 'value' => '1 Tahun'],
                    ['feature' => 'Jumlah Produk', 'value' => 'hingga 30 Item'],
                    ['feature' => 'Email Bisnis', 'value' => 'Gratis'],
                    ['feature' => 'Payment Gateway', 'value' => 'Ya'],
                    ['feature' => 'Cek Ongkir Otomatis', 'value' => '-'],
                    ['feature' => 'Revisi Desain', 'value' => '3x'],
                    ['feature' => 'Maintenance Gratis', 'value' => '1 Bulan'],
                ],
                'growth' => [
                    ['feature' => 'SSL Certificate', 'value' => 'Gratis'],
                    ['feature' => 'Domain & Hosting', 'value' => '1 Tahun'],
                    ['feature' => 'Jumlah Produk', 'value' => 'hingga 100 Item'],
                    ['feature' => 'Email Bisnis', 'value' => 'Gratis'],
                    ['feature' => 'Payment Gateway', 'value' => 'Ya'],
                    ['feature' => 'Cek Ongkir Otomatis', 'value' => 'Ya'],
                    ['feature' => 'Revisi Desain', 'value' => '5x'],
                    ['feature' => 'Maintenance Gratis', 'value' => '2 Bulan'],
                ],
                'scale' => [
                    ['feature' => 'SSL Certificate', 'value' => 'Gratis'],
                    ['feature' => 'Domain & Hosting', 'value' => '1 Tahun'],
                    ['feature' => 'Jumlah Produk', 'value' => 'Unlimited'],
                    ['feature' => 'Email Bisnis', 'value' => 'Gratis'],
                    ['feature' => 'Payment Gateway', 'value' => 'Ya'],
                    ['feature' => 'Cek Ongkir Otomatis', 'value' => 'Ya'],
                    ['feature' => 'Revisi Desain', 'value' => '7x'],
                    ['feature' => 'Maintenance Gratis', 'value' => '3 Bulan'],
                ],
            ],
            'sistem_informasi' => [
                'seed' => [
                    ['feature' => 'SSL Certificate', 'value' => 'Gratis'],
                    ['feature' => 'Domain & Hosting', 'value' => '1 Tahun'],
                    ['feature' => 'Role & Modul', 'value' => '1 Role, CRUD Sederhana'],
                    ['feature' => 'Email Bisnis', 'value' => 'Gratis'],
                    ['feature' => 'Notifikasi WA/Email', 'value' => '-'],
                    ['feature' => 'Revisi Sistem', 'value' => '2x'],
                    ['feature' => 'Maintenance Gratis', 'value' => '1 Bulan'],
                    ['feature' => 'Garansi Bug-Fixing', 'value' => '1 Bulan'],
                ],
                'growth' => [
                    ['feature' => 'SSL Certificate', 'value' => 'Gratis'],
                    ['feature' => 'Domain & Hosting', 'value' => '1 Tahun'],
                    ['feature' => 'Role & Modul', 'value' => 'Multi-Role, 2-3 Modul'],
                    ['feature' => 'Email Bisnis', 'value' => 'Gratis'],
                    ['feature' => 'Notifikasi WA/Email', 'value' => 'Ya'],
                    ['feature' => 'Revisi Sistem', 'value' => '4x'],
                    ['feature' => 'Maintenance Gratis', 'value' => '2 Bulan'],
                    ['feature' => 'Garansi Bug-Fixing', 'value' => '2 Bulan'],
                ],
                'scale' => [
                    ['feature' => 'SSL Certificate', 'value' => 'Gratis'],
                    ['feature' => 'Domain & Hosting', 'value' => '1 Tahun'],
                    ['feature' => 'Role & Modul', 'value' => 'Modul Lengkap + 3 Integrasi API'],
                    ['feature' => 'Email Bisnis', 'value' => 'Gratis'],
                    ['feature' => 'Notifikasi WA/Email', 'value' => 'Ya'],
                    ['feature' => 'Revisi Sistem', 'value' => '6x'],
                    ['feature' => 'Maintenance Gratis', 'value' => '3 Bulan'],
                    ['feature' => 'Garansi Bug-Fixing', 'value' => '3 Bulan'],
                ],
            ],
        ];

        foreach ($rows as $category => $tiers) {
            foreach ($tiers as $tier => $comparisonFeatures) {
                PricingPackage::where('category', $category)
                    ->where('tier', $tier)
                    ->update(['comparison_features' => $comparisonFeatures]);
            }
        }
    }

    public function down(): void
    {
        PricingPackage::query()->update(['comparison_features' => null]);
    }
};
