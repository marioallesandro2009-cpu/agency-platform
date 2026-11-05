<?php

use App\Models\Faq;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Fills out the FAQ section so it front-loads the objections that
     * otherwise get asked over chat first: what to prepare, timeline,
     * order/payment flow (DP %), revision flexibility, one-time vs
     * recurring cost, and who manages domain/hosting.
     */
    public function up(): void
    {
        // Existing FAQs: keep the question text as the update key so we
        // don't create duplicates, but sharpen the answer + reorder.
        Faq::updateOrCreate(
            ['question' => 'Apakah harga sudah termasuk domain dan hosting?'],
            [
                'answer' => 'Ya. Domain & hosting tahun pertama sudah termasuk dalam harga paket, dan kami yang mengurus penuh proses setup serta pengelolaan teknisnya — Anda tidak perlu membuat akun atau login ke panel hosting sendiri. Domain tetap terdaftar atas nama Anda (data WHOIS Anda), kami hanya mengelola sisi teknisnya.',
                'category' => 'pricing',
                'order' => 6,
            ]
        );

        Faq::updateOrCreate(
            ['question' => 'Apakah bisa request revisi desain?'],
            [
                'question' => 'Apakah revisi desain dibatasi?',
                'answer' => 'Setiap paket sudah mencakup jumlah revisi standar (lihat tabel fitur di halaman Harga) — tapi kami fleksibel selama masukan masih dalam scope kebutuhan awal, bukan sistem kaku "revisi habis, bayar lagi". Perubahan besar di luar scope awal (mis. ganti struktur/fitur total) akan kami diskusikan terpisah.',
                'category' => 'process',
                'order' => 4,
            ]
        );

        // Reorder only: these rows have no new answer, so on a fresh database
        // (where they do not exist yet) there is nothing to do; FaqSeeder adds them.
        Faq::where('question', 'Berapa lama waktu pengerjaan website?')->update(['category' => 'process', 'order' => 2]);

        Faq::where('question', 'Apakah website yang dibuat sudah SEO friendly?')->update(['category' => 'seo', 'order' => 7]);

        Faq::where('question', 'Bagaimana cara memulai konsultasi?')->update(['category' => 'general', 'order' => 8]);

        // New FAQs.
        Faq::updateOrCreate(
            ['question' => 'Apa saja yang perlu disiapkan sebelum website mulai dikerjakan?'],
            [
                'answer' => 'Idealnya siapkan logo (kalau sudah ada), konten teks (deskripsi usaha, daftar produk/layanan), foto-foto produk atau usaha, dan referensi desain kalau ada gambaran tertentu. Belum siap semua juga tidak masalah — tim kami bisa bantu susun kontennya bersama Anda saat konsultasi.',
                'category' => 'process',
                'order' => 1,
            ]
        );

        Faq::updateOrCreate(
            ['question' => 'Bagaimana cara order dan pembayarannya? Apakah perlu DP?'],
            [
                'answer' => 'Setelah paket dan kebutuhan disepakati saat konsultasi, order dikonfirmasi dengan DP 50% untuk memulai pengerjaan. Sisa 50% dilunasi setelah website selesai dan siap sebelum go-live. Pembayaran via transfer ke rekening resmi kami.',
                'category' => 'pricing',
                'order' => 3,
            ]
        );

        Faq::updateOrCreate(
            ['question' => 'Apakah harga berlaku sekali bayar atau ada biaya tahun berikutnya?'],
            [
                'answer' => 'Harga paket adalah biaya sekali bayar untuk pengerjaan website plus domain & hosting tahun pertama. Mulai tahun kedua hanya ada biaya renewal domain & hosting tahunan (rinciannya ada di halaman Harga) — tidak ada biaya development ulang selama Anda tidak minta penambahan fitur baru.',
                'category' => 'pricing',
                'order' => 5,
            ]
        );
    }

    public function down(): void
    {
        Faq::whereIn('question', [
            'Apa saja yang perlu disiapkan sebelum website mulai dikerjakan?',
            'Bagaimana cara order dan pembayarannya? Apakah perlu DP?',
            'Apakah harga berlaku sekali bayar atau ada biaya tahun berikutnya?',
        ])->delete();
    }
};
