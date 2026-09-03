<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            ["question" => "Apa saja yang perlu disiapkan sebelum website mulai dikerjakan?", "answer" => "Idealnya siapkan logo (kalau sudah ada), konten teks (deskripsi usaha, daftar produk/layanan), foto-foto produk atau usaha, dan referensi desain kalau ada gambaran tertentu. Belum siap semua juga tidak masalah — tim kami bisa bantu susun kontennya bersama Anda saat konsultasi.", "category" => "process", "order" => 1],
            ["question" => "Berapa lama waktu pengerjaan website?", "answer" => "Umumnya 1-3 minggu tergantung kompleksitas fitur dan jumlah halaman. Landing page bisa lebih cepat, sedangkan sistem custom butuh waktu lebih lama.", "category" => "process", "order" => 2],
            ["question" => "Bagaimana cara order dan pembayarannya? Apakah perlu DP?", "answer" => "Setelah paket dan kebutuhan disepakati saat konsultasi, order dikonfirmasi dengan DP 50% untuk memulai pengerjaan. Sisa 50% dilunasi setelah website selesai dan siap sebelum go-live. Pembayaran via transfer ke rekening resmi kami.", "category" => "pricing", "order" => 3],
            ["question" => "Apakah revisi desain dibatasi?", "answer" => "Setiap paket sudah mencakup jumlah revisi standar (lihat tabel fitur di halaman Harga) — tapi kami fleksibel selama masukan masih dalam scope kebutuhan awal, bukan sistem kaku \"revisi habis, bayar lagi\". Perubahan besar di luar scope awal (mis. ganti struktur/fitur total) akan kami diskusikan terpisah.", "category" => "process", "order" => 4],
            ["question" => "Apakah harga berlaku sekali bayar atau ada biaya tahun berikutnya?", "answer" => "Harga paket adalah biaya sekali bayar untuk pengerjaan website plus domain & hosting tahun pertama. Mulai tahun kedua hanya ada biaya renewal domain & hosting tahunan (rinciannya ada di halaman Harga) — tidak ada biaya development ulang selama Anda tidak minta penambahan fitur baru.", "category" => "pricing", "order" => 5],
            ["question" => "Apakah harga sudah termasuk domain dan hosting?", "answer" => "Ya. Domain & hosting tahun pertama sudah termasuk dalam harga paket, dan kami yang mengurus penuh proses setup serta pengelolaan teknisnya — Anda tidak perlu membuat akun atau login ke panel hosting sendiri. Domain tetap terdaftar atas nama Anda (data WHOIS Anda), kami hanya mengelola sisi teknisnya.", "category" => "pricing", "order" => 6],
            ["question" => "Apakah website yang dibuat sudah SEO friendly?", "answer" => "Ya, kami menerapkan dasar-dasar SEO seperti struktur heading yang benar, meta tag, dan kecepatan loading yang baik.", "category" => "seo", "order" => 7],
            ["question" => "Bagaimana cara memulai konsultasi?", "answer" => "Anda bisa mengisi form konsultasi atau menghubungi kami langsung lewat WhatsApp untuk mendiskusikan kebutuhan website Anda.", "category" => "general", "order" => 8],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(["question" => $faq["question"]], $faq);
        }
    }
}