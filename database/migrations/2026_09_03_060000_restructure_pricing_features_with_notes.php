<?php

use App\Models\PricingPackage;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Rewrites the per-tier feature checklist from a flat array of strings
     * to an array of {label, note} objects (so value-adds like "senilai
     * Rp500rb" can render as a smaller/muted line under the feature),
     * and adds estimated_days + value_note per tier. Items that are
     * standard across every tier/category (email bisnis, responsive, SEO
     * friendly, custom design) are dropped here — they move to a single
     * "included in all packages" block instead of repeating on every card.
     *
     * Prices (price, price_prefix, price_suffix, renewal_price, tagline,
     * badge, etc.) are untouched — only features/estimated_days/value_note.
     */
    public function up(): void
    {
        $domainHosting = ['label' => 'Domain + Hosting 1 Tahun', 'note' => 'senilai Rp500rb, gratis tahun pertama'];

        $tiers = [
            'landing_page' => [
                'seed' => [
                    'estimated_days' => '5 hari kerja',
                    'value_note' => 'Termasuk domain, hosting, dan email bisnis senilai Rp650rb',
                    'features' => [
                        ['label' => '1 Section Utama (hero + tombol aksi)', 'note' => null],
                        $domainHosting,
                        ['label' => 'Integrasi tombol WhatsApp', 'note' => null],
                        ['label' => 'Maintenance gratis 2 minggu', 'note' => null],
                        ['label' => 'Revisi desain 2x', 'note' => null],
                    ],
                ],
                'growth' => [
                    'estimated_days' => '7 hari kerja',
                    'value_note' => 'Termasuk domain, hosting, dan email bisnis senilai Rp650rb',
                    'features' => [
                        ['label' => '3-4 Section', 'note' => null],
                        $domainHosting,
                        ['label' => 'Integrasi Google Analytics & Meta Pixel', 'note' => null],
                        ['label' => 'Maintenance gratis 1 bulan', 'note' => null],
                        ['label' => 'Revisi desain 4x', 'note' => null],
                    ],
                ],
                'scale' => [
                    'estimated_days' => '10 hari kerja',
                    'value_note' => 'Termasuk domain, hosting, dan email bisnis senilai Rp650rb',
                    'features' => [
                        ['label' => '5+ Section dengan animasi custom', 'note' => null],
                        $domainHosting,
                        ['label' => 'Live chat widget', 'note' => null],
                        ['label' => 'Struktur siap A/B testing', 'note' => null],
                        ['label' => 'Maintenance gratis 2 bulan', 'note' => null],
                        ['label' => 'Revisi desain 7x', 'note' => null],
                    ],
                ],
            ],
            'company_profile' => [
                'seed' => [
                    'estimated_days' => '10 hari kerja',
                    'value_note' => 'Termasuk domain, hosting, dan email bisnis senilai Rp650rb',
                    'features' => [
                        ['label' => '5 Halaman', 'note' => null],
                        $domainHosting,
                        ['label' => 'Maintenance gratis 1 bulan', 'note' => null],
                        ['label' => 'Revisi desain 3x', 'note' => null],
                    ],
                ],
                'growth' => [
                    'estimated_days' => '14 hari kerja',
                    'value_note' => 'Termasuk domain, hosting, dan email bisnis senilai Rp650rb',
                    'features' => [
                        ['label' => 'Halaman Unlimited', 'note' => null],
                        $domainHosting,
                        ['label' => 'Blog + CMS artikel', 'note' => null],
                        ['label' => 'Maintenance gratis 2 bulan', 'note' => null],
                        ['label' => 'Revisi desain 5x', 'note' => null],
                    ],
                ],
                'scale' => [
                    'estimated_days' => '21 hari kerja',
                    'value_note' => 'Termasuk domain, hosting, email bisnis, dan paket SEO awal senilai Rp1,4jt',
                    'features' => [
                        ['label' => 'Halaman Unlimited', 'note' => null],
                        $domainHosting,
                        ['label' => 'Blog + CMS artikel', 'note' => null],
                        ['label' => 'Multi bahasa dengan terjemahan manual', 'note' => null],
                        ['label' => 'Riset kata kunci + 5 artikel SEO awal', 'note' => 'senilai Rp750rb'],
                        ['label' => 'Sesi strategi konten 60 menit', 'note' => null],
                        ['label' => 'Laporan performa bulanan selama 3 bulan', 'note' => null],
                        ['label' => 'Maintenance gratis 3 bulan', 'note' => null],
                        ['label' => 'Revisi desain 7x', 'note' => null],
                    ],
                ],
            ],
            'ecommerce' => [
                'seed' => [
                    'estimated_days' => '14 hari kerja',
                    'value_note' => 'Termasuk domain, hosting, email bisnis, dan payment gateway senilai Rp950rb',
                    'features' => [
                        ['label' => 'Katalog hingga 30 produk', 'note' => null],
                        $domainHosting,
                        ['label' => 'Payment gateway terpasang dan diuji', 'note' => 'senilai Rp300rb'],
                        ['label' => 'Dashboard laporan penjualan dasar', 'note' => null],
                        ['label' => 'Maintenance gratis 1 bulan', 'note' => null],
                        ['label' => 'Revisi desain 3x', 'note' => null],
                    ],
                ],
                'growth' => [
                    'estimated_days' => '21 hari kerja',
                    'value_note' => 'Termasuk domain, hosting, email bisnis, payment gateway, dan cek ongkir senilai Rp1,75jt',
                    'features' => [
                        ['label' => 'Katalog hingga 100 produk', 'note' => null],
                        $domainHosting,
                        ['label' => 'Payment gateway terpasang dan diuji', 'note' => 'senilai Rp300rb'],
                        ['label' => 'Cek ongkir otomatis (RajaOngkir/Biteship)', 'note' => 'senilai Rp800rb'],
                        ['label' => 'Dashboard analitik penjualan', 'note' => null],
                        ['label' => 'Maintenance gratis 2 bulan', 'note' => null],
                        ['label' => 'Revisi desain 5x', 'note' => null],
                    ],
                ],
                'scale' => [
                    'estimated_days' => '30 hari kerja',
                    'value_note' => 'Termasuk domain, hosting, email bisnis, payment gateway, cek ongkir, dan notifikasi WhatsApp senilai Rp2,75jt',
                    'features' => [
                        ['label' => 'Produk Unlimited', 'note' => null],
                        $domainHosting,
                        ['label' => 'Payment gateway terpasang dan diuji', 'note' => 'senilai Rp300rb'],
                        ['label' => 'Cek ongkir otomatis (RajaOngkir/Biteship)', 'note' => 'senilai Rp800rb'],
                        ['label' => 'Dashboard analitik penjualan + export Excel', 'note' => null],
                        ['label' => 'Notifikasi WhatsApp otomatis untuk pesanan', 'note' => 'senilai Rp1jt; biaya langganan layanan WhatsApp ditanggung pemilik website'],
                        ['label' => 'Maintenance gratis 3 bulan', 'note' => null],
                        ['label' => 'Revisi desain 7x', 'note' => null],
                    ],
                ],
            ],
            'sistem_informasi' => [
                'seed' => [
                    'estimated_days' => '14 hari kerja',
                    'value_note' => 'Termasuk domain, hosting, dan email bisnis senilai Rp650rb',
                    'features' => [
                        ['label' => 'CRUD sederhana, 1 role pengguna', 'note' => null],
                        $domainHosting,
                        ['label' => 'Garansi bug-fixing 1 bulan', 'note' => null],
                        ['label' => 'Maintenance gratis 1 bulan', 'note' => null],
                        ['label' => 'Revisi 2x', 'note' => null],
                    ],
                ],
                'growth' => [
                    'estimated_days' => '21 hari kerja',
                    'value_note' => 'Termasuk domain, hosting, email bisnis, dan dokumentasi senilai Rp1,15jt',
                    'features' => [
                        ['label' => 'Multi-role, 2-3 modul', 'note' => null],
                        $domainHosting,
                        ['label' => 'Dashboard statistik', 'note' => null],
                        ['label' => 'Notifikasi WhatsApp / Email', 'note' => null],
                        ['label' => 'Dokumentasi & user manual', 'note' => 'senilai Rp500rb'],
                        ['label' => 'Garansi bug-fixing 2 bulan', 'note' => null],
                        ['label' => 'Maintenance gratis 2 bulan', 'note' => null],
                        ['label' => 'Revisi 4x', 'note' => null],
                    ],
                ],
                'scale' => [
                    'estimated_days' => '40 hari kerja',
                    'value_note' => 'Termasuk domain, hosting, email bisnis, dokumentasi, dan pelatihan tim senilai Rp1,65jt',
                    'features' => [
                        ['label' => 'Modul lengkap, hingga 3 integrasi API', 'note' => null],
                        $domainHosting,
                        ['label' => 'Dashboard insight', 'note' => null],
                        ['label' => 'Notifikasi WhatsApp / Email', 'note' => null],
                        ['label' => 'Dokumentasi & user manual', 'note' => 'senilai Rp500rb'],
                        ['label' => 'Pelatihan tim hingga 5 orang', 'note' => 'senilai Rp500rb'],
                        ['label' => 'Garansi bug-fixing 3 bulan', 'note' => null],
                        ['label' => 'Maintenance gratis 3 bulan', 'note' => null],
                        ['label' => 'Revisi 6x', 'note' => null],
                    ],
                ],
            ],
        ];

        foreach ($tiers as $category => $tierMap) {
            foreach ($tierMap as $tier => $data) {
                PricingPackage::where('category', $category)
                    ->where('tier', $tier)
                    ->update([
                        'features' => $data['features'],
                        'estimated_days' => $data['estimated_days'],
                        'value_note' => $data['value_note'],
                    ]);
            }
        }
    }

    public function down(): void
    {
        PricingPackage::query()->update(['estimated_days' => null, 'value_note' => null]);
    }
};
