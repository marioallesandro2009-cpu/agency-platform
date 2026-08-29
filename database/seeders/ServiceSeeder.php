<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                "name" => "Website UMKM",
                "slug" => "jasa-website-umkm",
                "short_description" => "Website sederhana dan terjangkau untuk UMKM yang ingin hadir online.",
                "badge_label" => "Paling Populer",
                "description" => "Solusi website untuk UMKM yang membutuhkan kehadiran online yang cepat, mudah dikelola, dan terjangkau.",
                "long_description" => "<h2>Apa itu Jasa Pembuatan Website UMKM?</h2>"
                    . "<p>Jasa pembuatan website UMKM adalah layanan membangun website usaha kecil dan menengah yang dirancang khusus untuk kebutuhan bisnis rumahan, toko kelontong, warung, hingga usaha rumahan yang mulai berkembang. Berbeda dari website korporat yang kompleks, website UMKM difokuskan pada kecepatan pembuatan, kemudahan pengelolaan konten sendiri, dan biaya yang masuk akal untuk skala usaha kecil.</p>"
                    . "<h2>Kenapa Bisnis Anda Membutuhkan Website UMKM?</h2>"
                    . "<p>Banyak UMKM masih mengandalkan media sosial sebagai satu-satunya kehadiran online, padahal media sosial punya keterbatasan: algoritma yang berubah-ubah, jangkauan organik yang menurun, dan tidak ada tempat resmi untuk menampilkan katalog produk secara rapi. Website UMKM memberikan alamat digital permanen yang meningkatkan kredibilitas di mata calon pelanggan, sekaligus jadi kanal yang sepenuhnya Anda kendalikan tanpa bergantung pada kebijakan platform pihak ketiga.</p>"
                    . "<h2>Apa yang Anda Dapatkan dari Layanan Ini?</h2>"
                    . "<p>Kami membangun website UMKM yang fokus pada tiga hal: kecepatan akses di perangkat mobile (karena mayoritas pengunjung UMKM berasal dari HP), integrasi WhatsApp agar pengunjung bisa langsung bertanya atau order, dan CMS yang mudah dipakai sehingga Anda bisa memperbarui produk atau harga sendiri tanpa perlu memanggil developer setiap saat.</p>",
                "icon" => "store",
                "problems" => [
                    "Pelanggan sulit menemukan info bisnis di luar jam operasional media sosial",
                    "Tidak ada tempat resmi untuk menampilkan produk dan harga",
                    "Sulit bersaing dengan bisnis yang sudah punya website",
                ],
                "features" => [
                    "Desain responsif untuk mobile & desktop",
                    "Integrasi WhatsApp langsung dari halaman produk",
                    "SEO dasar (meta title, deskripsi, sitemap)",
                    "CMS mudah digunakan tanpa perlu coding",
                    "Katalog produk/layanan dengan foto & harga",
                    "Peta lokasi Google Maps terintegrasi",
                    "Halaman testimoni pelanggan",
                    "Kecepatan loading dioptimasi untuk koneksi mobile",
                ],
                "detail_features" => ["Katalog produk/layanan", "Integrasi WhatsApp langsung", "Peta lokasi Google Maps", "Optimasi SEO dasar", "CMS untuk update konten sendiri", "Desain mobile-friendly"],
                "benefits" => [
                    "Meningkatkan kepercayaan pelanggan terhadap bisnis Anda",
                    "Jangkauan pasar lebih luas di luar radius toko fisik",
                    "Pengunjung bisa order langsung via WhatsApp tanpa hambatan",
                    "Tidak bergantung pada algoritma media sosial",
                    "Bisa dikelola dan diperbarui sendiri tanpa biaya tambahan",
                ],
                "target_audience" => "Pelaku UMKM, pemilik usaha rumahan, toko kelontong, dan usaha kecil-menengah yang baru mulai membangun kehadiran online atau ingin lepas dari ketergantungan penuh pada media sosial.",
                "process_steps" => ["Konsultasi Kebutuhan", "Desain Tampilan", "Isi Konten & Produk", "Review & Revisi", "Launching Website"],
                "faqs" => [
                    ["question" => "Berapa lama proses pembuatan website UMKM?", "answer" => "Umumnya 1-2 minggu sejak konten dan materi (logo, foto produk, deskripsi) lengkap diterima."],
                    ["question" => "Apakah saya bisa update produk dan harga sendiri?", "answer" => "Bisa. Website dilengkapi CMS sederhana sehingga Anda bisa menambah atau mengubah produk, harga, dan foto tanpa bantuan developer."],
                    ["question" => "Apakah websitenya sudah bisa muncul di Google?", "answer" => "Website sudah dilengkapi SEO dasar (meta title, deskripsi, sitemap) sehingga bisa terindeks Google. Untuk bersaing di kata kunci kompetitif, biasanya perlu optimasi SEO lanjutan."],
                    ["question" => "Apakah ada biaya bulanan setelah website jadi?", "answer" => "Ada biaya hosting & domain tahunan yang wajar. Kami juga menyediakan paket maintenance opsional bila Anda tidak ingin repot mengelola sendiri."],
                ],
                "cta_text" => "Konsultasi Gratis",
                "cta_url" => "/konsultasi",
                "is_featured" => true,
                "order" => 1,
                "is_published" => true,
            ],
            [
                "name" => "Company Profile",
                "slug" => "jasa-website-company-profile",
                "short_description" => "Website profesional untuk membangun kredibilitas perusahaan Anda.",
                "badge_label" => "Rekomendasi Korporat",
                "description" => "Website company profile yang menampilkan identitas, layanan, dan portofolio perusahaan secara profesional.",
                "long_description" => "<h2>Apa itu Jasa Pembuatan Website Company Profile?</h2>"
                    . "<p>Jasa pembuatan website company profile adalah layanan membangun website perusahaan yang berfungsi sebagai kartu nama digital — menampilkan identitas, layanan, tim, dan rekam jejak perusahaan secara terstruktur dan profesional. Website company profile berbeda dari landing page sederhana karena dirancang untuk meyakinkan audiens korporat: calon klien B2B, investor, mitra bisnis, atau bahkan calon karyawan.</p>"
                    . "<h2>Kenapa Perusahaan Anda Membutuhkan Website Company Profile?</h2>"
                    . "<p>Calon klien atau mitra bisnis hampir selalu melakukan riset online sebelum memutuskan kerja sama. Tanpa website company profile yang profesional, mereka hanya menemukan media sosial atau bahkan tidak menemukan apa-apa — yang menimbulkan keraguan terhadap kredibilitas perusahaan. Website company profile menjawab pertanyaan dasar calon klien: siapa perusahaan ini, layanan apa yang ditawarkan, dan proyek apa saja yang pernah dikerjakan, semuanya dalam satu tempat yang rapi dan mudah diakses kapan saja.</p>"
                    . "<h2>Apa yang Anda Dapatkan dari Layanan Ini?</h2>"
                    . "<p>Kami membangun struktur halaman yang menonjolkan kredibilitas: profil dan visi-misi perusahaan, halaman tim atau struktur organisasi, portofolio proyek yang pernah dikerjakan, hingga blog atau berita terbaru — semuanya dikelola sendiri lewat CMS tanpa perlu bantuan developer setiap kali ada pembaruan konten.</p>",
                "icon" => "building",
                "problems" => [
                    "Calon klien ragu karena tidak ada informasi resmi perusahaan secara online",
                    "Sulit menunjukkan portofolio proyek secara terstruktur",
                    "Tidak ada tempat mempublikasikan berita atau pencapaian perusahaan",
                ],
                "features" => [
                    "Desain profesional sesuai identitas brand",
                    "Halaman tim & struktur organisasi",
                    "Blog/berita perusahaan terintegrasi",
                    "SEO friendly untuk pencarian nama perusahaan & layanan",
                    "Halaman portofolio proyek dengan studi kasus",
                    "Formulir kontak & permintaan penawaran",
                    "Integrasi Google Maps kantor/cabang",
                    "Sertifikasi/penghargaan perusahaan (bila ada)",
                ],
                "detail_features" => ["Halaman tentang & visi misi", "Halaman tim/struktur organisasi", "Portofolio proyek", "Blog/berita perusahaan", "Formulir kontak", "SEO friendly"],
                "benefits" => [
                    "Kredibilitas bisnis meningkat di mata calon klien & mitra",
                    "Mudah ditemukan calon klien lewat pencarian Google",
                    "Portofolio tersaji rapi tanpa perlu kirim file terpisah",
                    "Memudahkan proses rekrutmen dengan halaman karier",
                    "Berita/pencapaian perusahaan terdokumentasi secara publik",
                ],
                "target_audience" => "Perusahaan, agensi, konsultan, dan penyedia jasa B2B yang perlu membangun kredibilitas profesional di hadapan calon klien, mitra bisnis, atau investor.",
                "process_steps" => ["Konsultasi & Riset Brand", "Penyusunan Struktur Konten", "Desain & Development", "Review Bersama Tim", "Launching & Pelatihan CMS"],
                "faqs" => [
                    ["question" => "Apakah desain bisa disesuaikan dengan brand guideline perusahaan kami?", "answer" => "Bisa. Kami menyesuaikan warna, logo, dan gaya visual sesuai brand guideline yang sudah dimiliki perusahaan."],
                    ["question" => "Berapa lama waktu pengerjaan website company profile?", "answer" => "Umumnya 2-4 minggu tergantung jumlah halaman dan kompleksitas konten yang perlu disiapkan."],
                    ["question" => "Apakah bisa menambah halaman karier untuk rekrutmen?", "answer" => "Bisa ditambahkan sebagai halaman tambahan sesuai kebutuhan, termasuk formulir pengiriman lamaran."],
                    ["question" => "Apakah website bisa dikelola oleh tim internal setelah selesai?", "answer" => "Bisa. Kami menyediakan CMS yang mudah dipakai serta sesi pelatihan singkat untuk tim internal Anda."],
                ],
                "cta_text" => "Konsultasi Gratis",
                "cta_url" => "/konsultasi",
                "is_featured" => true,
                "order" => 2,
                "is_published" => true,
            ],
            [
                "name" => "Toko Online",
                "slug" => "jasa-toko-online",
                "short_description" => "Sistem e-commerce lengkap untuk mulai berjualan online.",
                "badge_label" => "Solusi Lengkap",
                "description" => "Website toko online dengan sistem katalog produk, keranjang belanja, dan manajemen pesanan.",
                "long_description" => "<h2>Apa itu Jasa Toko Online?</h2>"
                    . "<p>Jasa toko online adalah layanan pembuatan website e-commerce lengkap dengan sistem katalog produk, keranjang belanja, checkout, dan manajemen pesanan — memungkinkan bisnis Anda berjualan secara mandiri tanpa harus sepenuhnya bergantung pada marketplace pihak ketiga.</p>"
                    . "<h2>Kenapa Bisnis Anda Membutuhkan Toko Online Sendiri?</h2>"
                    . "<p>Berjualan lewat marketplace memang praktis di awal, tapi ada trade-off: komisi yang mengurangi margin keuntungan, branding toko yang tenggelam di antara ribuan kompetitor pada platform yang sama, dan data pelanggan yang tidak sepenuhnya menjadi milik bisnis Anda. Toko online sendiri memberi kendali penuh atas branding, harga, promosi, dan hubungan langsung dengan pelanggan — sekaligus tetap bisa dijalankan berdampingan dengan marketplace yang sudah ada.</p>"
                    . "<h2>Apa yang Anda Dapatkan dari Layanan Ini?</h2>"
                    . "<p>Kami membangun toko online dengan katalog produk lengkap dengan varian (ukuran, warna, stok), keranjang belanja, checkout terintegrasi payment gateway, serta dashboard admin untuk mengelola pesanan dan stok — semua dalam satu sistem yang bisa Anda pantau kapan saja.</p>",
                "icon" => "shopping-cart",
                "problems" => [
                    "Komisi marketplace mengurangi margin keuntungan",
                    "Branding toko tenggelam di antara kompetitor pada satu platform",
                    "Data pelanggan tidak sepenuhnya menjadi milik bisnis",
                ],
                "features" => [
                    "Katalog produk dengan varian (ukuran, warna, stok)",
                    "Keranjang belanja & checkout terintegrasi",
                    "Manajemen pesanan & status pengiriman",
                    "Integrasi payment gateway (transfer, e-wallet, QRIS)",
                    "Dashboard admin untuk pantau penjualan",
                    "Sistem diskon & kode voucher",
                    "Integrasi ongkir otomatis berdasarkan lokasi",
                    "Notifikasi pesanan via WhatsApp/email",
                ],
                "detail_features" => ["Katalog produk dengan varian", "Keranjang belanja", "Checkout terintegrasi", "Payment gateway", "Manajemen pesanan & stok", "Dashboard admin penjualan"],
                "benefits" => [
                    "Penjualan bisa berlangsung 24 jam tanpa perlu dijaga manual",
                    "Jangkauan pasar nasional tanpa batas lokasi toko fisik",
                    "Margin keuntungan lebih baik tanpa potongan komisi marketplace",
                    "Data dan hubungan pelanggan sepenuhnya milik bisnis Anda",
                    "Branding toko lebih menonjol dibanding di marketplace",
                ],
                "target_audience" => "Pemilik brand dan penjual online yang sudah berjualan di marketplace atau media sosial dan ingin memiliki kanal penjualan sendiri dengan kendali penuh atas branding dan data pelanggan.",
                "process_steps" => ["Konsultasi Model Bisnis", "Setup Katalog & Payment Gateway", "Desain & Development", "Uji Coba Transaksi", "Launching Toko Online"],
                "faqs" => [
                    ["question" => "Payment gateway apa saja yang didukung?", "answer" => "Umumnya mendukung transfer bank, e-wallet, dan QRIS melalui payment gateway pihak ketiga sesuai kebutuhan bisnis Anda."],
                    ["question" => "Apakah stok produk bisa dikelola otomatis?", "answer" => "Bisa. Dashboard admin memungkinkan Anda mengatur stok per varian produk dan stok akan otomatis berkurang saat ada pesanan masuk."],
                    ["question" => "Apakah toko online bisa terhubung dengan marketplace yang sudah ada?", "answer" => "Toko online berjalan independen dari marketplace, sehingga Anda bisa menjalankan keduanya secara bersamaan sebagai kanal penjualan terpisah."],
                    ["question" => "Berapa lama waktu pengerjaan toko online?", "answer" => "Umumnya 3-5 minggu tergantung jumlah produk dan kompleksitas fitur pembayaran/pengiriman yang dibutuhkan."],
                ],
                "cta_text" => "Konsultasi Gratis",
                "cta_url" => "/konsultasi",
                "is_featured" => true,
                "order" => 3,
                "is_published" => true,
            ],
            [
                "name" => "Custom Web Development",
                "slug" => "jasa-web-development",
                "short_description" => "Aplikasi web custom sesuai kebutuhan bisnis spesifik Anda.",
                "badge_label" => "Paling Fleksibel",
                "description" => "Pengembangan sistem web custom seperti CRM, sistem booking, dashboard internal, dan integrasi API.",
                "long_description" => "<h2>Apa itu Custom Web Development?</h2>"
                    . "<p>Custom web development Indonesia adalah layanan pengembangan aplikasi web yang dirancang khusus mengikuti proses bisnis spesifik Anda — bukan template atau CMS siap pakai yang memaksa bisnis menyesuaikan diri dengan keterbatasan sistem. Cakupannya bisa berupa CRM, sistem booking, dashboard internal, hingga integrasi API antar sistem.</p>"
                    . "<h2>Kenapa Bisnis Anda Membutuhkan Pengembangan Web Custom?</h2>"
                    . "<p>Beberapa kebutuhan bisnis tidak bisa diselesaikan dengan software siap pakai: proses kerja yang unik, kebutuhan integrasi antar sistem/API yang kompleks, atau proses internal yang masih manual dan rawan human error (misalnya pencatatan pesanan lewat spreadsheet). Aplikasi web custom dirancang mengikuti alur kerja nyata tim Anda, sehingga adopsi lebih cepat dan efisiensi meningkat sejak hari pertama digunakan.</p>"
                    . "<h2>Apa yang Anda Dapatkan dari Layanan Ini?</h2>"
                    . "<p>Kami memulai dengan analisis kebutuhan bisnis secara mendalam, dilanjutkan desain sistem sesuai alur kerja Anda, pengembangan aplikasi, integrasi API pihak ketiga bila diperlukan, hingga dashboard pelaporan dan dukungan teknis pasca-launching agar sistem terus berjalan lancar.</p>",
                "icon" => "code",
                "problems" => [
                    "Proses bisnis unik yang tidak bisa diakomodasi software siap pakai",
                    "Kebutuhan integrasi antar sistem/API yang kompleks",
                    "Sistem internal manual yang rawan human error",
                ],
                "features" => [
                    "Analisis kebutuhan bisnis mendalam",
                    "Desain sistem sesuai alur kerja tim",
                    "Pengembangan aplikasi web custom end-to-end",
                    "Integrasi API pihak ketiga (payment, logistik, dll)",
                    "Dashboard & pelaporan data real-time",
                    "Manajemen user & hak akses (role-based)",
                    "Skalabilitas sistem untuk pertumbuhan bisnis",
                    "Dukungan teknis pasca-launching",
                ],
                "detail_features" => ["Analisis kebutuhan bisnis", "Desain sistem sesuai alur kerja", "Pengembangan aplikasi web custom", "Integrasi API pihak ketiga", "Dashboard & pelaporan", "Dukungan teknis pasca-launching"],
                "benefits" => [
                    "Sistem sesuai proses bisnis nyata, bukan sebaliknya",
                    "Skalabel mengikuti pertumbuhan bisnis ke depan",
                    "Mengurangi kesalahan manual lewat otomatisasi proses",
                    "Efisiensi waktu kerja tim internal meningkat",
                    "Kepemilikan penuh atas sistem dan data",
                ],
                "target_audience" => "Perusahaan dengan proses bisnis spesifik yang tidak terakomodasi software siap pakai, atau yang membutuhkan integrasi sistem/API kompleks antar platform yang sudah digunakan.",
                "process_steps" => ["Analisis Kebutuhan Bisnis", "Desain Sistem & Database", "Development Bertahap (Sprint)", "Testing & QA", "Deployment & Support"],
                "faqs" => [
                    ["question" => "Berapa lama waktu pengembangan aplikasi custom?", "answer" => "Bervariasi tergantung kompleksitas, umumnya 1-3 bulan. Kami memberikan estimasi detail setelah sesi analisis kebutuhan."],
                    ["question" => "Apakah bisa integrasi dengan sistem yang sudah kami pakai?", "answer" => "Bisa, selama sistem tersebut menyediakan API atau metode integrasi lain yang bisa diakses."],
                    ["question" => "Apakah ada dukungan setelah aplikasi selesai dikembangkan?", "answer" => "Ya, kami menyediakan dukungan teknis pasca-launching untuk memastikan sistem berjalan lancar dan menangani kebutuhan penyesuaian di kemudian hari."],
                    ["question" => "Bagaimana proses pembayaran untuk proyek custom?", "answer" => "Umumnya dibagi dalam beberapa termin mengikuti milestone pengembangan, disesuaikan dengan skala proyek."],
                ],
                "cta_text" => "Konsultasi Gratis",
                "cta_url" => "/konsultasi",
                "is_featured" => true,
                "order" => 4,
                "is_published" => true,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(["slug" => $service["slug"]], $service);
        }
    }
}
