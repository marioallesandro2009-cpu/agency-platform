@php
    $breadcrumbItems = [
        ['label' => 'Home', 'href' => '/'],
        ['label' => 'Jasa & Layanan', 'href' => '/jasa-website'],
        ['label' => $service->name],
    ];
    $consultUrl = '/konsultasi?service=' . rawurlencode('Software Custom');
    $whatsappUrl = ($settings['contact']['whatsapp_number'] ?? null)
        ? 'https://wa.me/' . $settings['contact']['whatsapp_number'] . '?text=' . rawurlencode('Halo, saya ingin menanyakan estimasi software custom untuk perusahaan kami.')
        : null;

    // The hero's point: these costs are real, but nobody has put a number on them.
    $ledger = [
        'Rekap dan laporan manual',
        'Input data yang sama berulang kali',
        'Selisih stok dan salah tagih',
        'Keputusan yang menunggu data',
    ];

    $divisions = ['Operasional', 'Keuangan', 'SDM', 'Sales', 'Gudang', 'Manajemen'];

    $findings = [
        ['title' => 'Jam kerja yang habis untuk memindahkan data', 'text' => 'Staf yang digaji untuk berpikir dan melayani pelanggan justru menyalin angka dari satu file ke file lain.', 'ask' => 'Berapa jam per minggu tim Anda menyusun laporan?'],
        ['title' => 'Kesalahan yang baru ketahuan belakangan', 'text' => 'Selisih stok, salah tagih, dan data ganda jarang tercatat sebagai biaya, padahal margin dan kepercayaan pelanggan yang menanggungnya.', 'ask' => 'Kapan terakhir angka antar-divisi berbeda?'],
        ['title' => 'Keputusan yang selalu terlambat', 'text' => 'Saat laporan selesai dirapikan, kondisi di lapangan sudah berubah. Manajemen memutuskan dengan data bulan lalu.', 'ask' => 'Bisakah Anda melihat kondisi hari ini tanpa bertanya?'],
        ['title' => 'Proses yang bergantung pada satu orang', 'text' => 'Saat karyawan kunci cuti atau keluar, pengetahuannya ikut pergi dan pekerjaan penting ikut berhenti.', 'ask' => 'Apa yang terhenti jika orang itu resign bulan depan?'],
    ];

    $checks = [
        'Laporan bulanan butuh lebih dari satu hari kerja untuk disusun',
        'Data yang sama diinput ulang di beberapa file atau aplikasi',
        'Angka antar-divisi sering berbeda dan harus dicocokkan manual',
        'Persetujuan tertahan karena menunggu tanda tangan atau balasan chat',
        'Biaya lisensi aplikasi terus naik mengikuti jumlah pengguna',
        'Ada proses penting yang hanya dipahami satu orang',
    ];

    $solutions = [
        ['unit' => 'Operasional', 'today' => 'Data pembelian, penjualan, dan stok tersebar di banyak tempat.', 'system' => 'ERP sesuai alur Anda'],
        ['unit' => 'SDM', 'today' => 'Rekap absensi, cuti, lembur, dan gaji menyita waktu setiap bulan.', 'system' => 'HRIS dan payroll'],
        ['unit' => 'Sales', 'today' => 'Prospek hilang karena follow-up tercatat di chat pribadi.', 'system' => 'CRM dan pipeline sales'],
        ['unit' => 'Gudang', 'today' => 'Stok di catatan berbeda dengan stok di rak.', 'system' => 'Inventory dan barcode'],
        ['unit' => 'Tim Lapangan', 'today' => 'Sales, teknisi, dan kurir baru melapor setelah kembali ke kantor.', 'system' => 'Aplikasi mobile Android dan iOS'],
        ['unit' => 'Layanan Klien', 'today' => 'Tim terus menjawab pertanyaan status dan permintaan dokumen.', 'system' => 'Portal klien dan vendor'],
        ['unit' => 'Administrasi', 'today' => 'Pengajuan dana dan pembelian tertahan menunggu tanda tangan.', 'system' => 'Approval dan e-office'],
        ['unit' => 'Manajemen', 'today' => 'Laporan terlambat dan data tiap sistem tidak terhubung.', 'system' => 'Dashboard dan integrasi'],
    ];

    $comparison = [
        ['label' => 'Tepat untuk', 'saas' => 'Kebutuhan umum', 'custom' => 'Proses khas perusahaan', 'team' => 'Software sebagai inti bisnis'],
        ['label' => 'Biaya awal', 'saas' => 'Rendah', 'custom' => 'Perlu investasi, bisa bertahap', 'team' => 'Rekrutmen dan gaji'],
        ['label' => 'Biaya berjalan', 'saas' => 'Sering mengikuti jumlah pengguna', 'custom' => 'Server dan perawatan', 'team' => 'Gaji tetap tim'],
        ['label' => 'Kecocokan proses', 'saas' => 'Anda menyesuaikan diri', 'custom' => 'Sistem mengikuti Anda', 'team' => 'Sistem mengikuti Anda'],
        ['label' => 'Mulai dipakai', 'saas' => 'Paling cepat', 'custom' => 'Sesuai tahap yang disepakati', 'team' => 'Setelah tim terbentuk'],
    ];

    $process = [
        ['title' => 'Diskusi Kebutuhan', 'output' => 'Gambaran masalah dan solusi'],
        ['title' => 'Pemetaan Proses', 'output' => 'Daftar modul dan estimasi tertulis'],
        ['title' => 'Rancangan Sistem', 'output' => 'Alur dan tampilan yang disepakati'],
        ['title' => 'Pengembangan Bertahap', 'output' => 'Modul yang bisa dicoba per tahap'],
        ['title' => 'Uji Coba dan Migrasi', 'output' => 'Pengujian tim Anda dan data lama yang pindah'],
        ['title' => 'Peluncuran', 'output' => 'Pelatihan, dokumentasi, dan pendampingan'],
    ];

    $stakeholders = [
        ['who' => 'Direksi', 'question' => 'Apakah investasinya terkendali?', 'answer' => 'Dibangun bertahap, dan setiap tahap diputuskan dari hasil tahap sebelumnya.'],
        ['who' => 'Legal', 'question' => 'Bagaimana kerahasiaan dan kepemilikan?', 'answer' => 'NDA sebelum diskusi detail. Source code, dokumentasi, dan akses diatur di kontrak.'],
        ['who' => 'IT', 'question' => 'Apakah aman dan bisa dirawat?', 'answer' => 'Hak akses per peran, catatan aktivitas, backup, dan teknologi yang umum dipakai.'],
        ['who' => 'Procurement', 'question' => 'Apakah mudah dievaluasi?', 'answer' => 'Penawaran tertulis per modul dan pembayaran per milestone.'],
    ];

    $serviceJsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => 'Jasa Pembuatan Software Custom',
        'serviceType' => 'Custom software development',
        'description' => $service->seo_description ?: $service->short_description,
        'url' => url('/jasa-software-custom'),
        'areaServed' => 'ID',
        'provider' => [
            '@type' => 'Organization',
            'name' => $settings['general']['website_name'] ?? null,
            'url' => url('/'),
        ],
    ];

    $faqJsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => collect($service->faqs ?? [])->map(fn ($faq) => [
            '@type' => 'Question',
            'name' => $faq['question'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']],
        ])->values()->all(),
    ];
@endphp

<x-layouts.public
    theme="space"
    accent="blue"
    :seo-title="$service->seo_title ?: $service->name"
    :seo-description="$service->seo_description ?: $service->short_description"
    :seo-image="$service->image"
    :seo-json-ld="[$serviceJsonLd, $faqJsonLd]"
>
    {{-- Hero: the claim on the left, an unfilled cost ledger on the right --}}
    <section class="corp-hero relative overflow-hidden pt-24 md:pt-28 pb-10 md:pb-14 border-b border-white/10">
        <img src="/images/software/server-rack.webp" alt="" aria-hidden="true" width="1280" height="852" class="corp-photo absolute inset-y-0 right-0 h-full w-full lg:w-3/4 object-cover">
        <div class="corp-photo-fade absolute inset-0" aria-hidden="true"></div>
        <div class="relative max-w-[1320px] mx-auto px-4 md:px-8">
            <x-public.breadcrumbs :items="$breadcrumbItems" variant="space" />
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <div class="lg:col-span-7">
                    <p data-reveal-load class="font-mono text-[11px] uppercase tracking-[0.14em] text-brand-300 mb-6">Jasa Pembuatan Software Custom &amp; Aplikasi</p>
                    <h1 data-reveal-load class="font-display font-bold text-[2.15rem] sm:text-5xl xl:text-[3.75rem] leading-[1.08] text-white mb-6 [text-wrap:balance]">Software Custom untuk Perusahaan yang Sudah Terlalu Lama Membayar Proses Manual</h1>
                    <p data-reveal-load style="--d: 120ms" class="text-lg text-white/70 leading-relaxed max-w-2xl mb-9">Biayanya tidak tercatat sebagai pengeluaran, tetapi Anda membayarnya setiap bulan lewat jam kerja, kesalahan, dan keputusan yang terlambat. Kami membangun sistem yang mengikuti cara kerja perusahaan Anda.</p>
                    <div data-reveal-load style="--d: 200ms" class="flex flex-col sm:flex-row sm:flex-wrap gap-3">
                        <a href="{{ $consultUrl }}" class="btn-space btn-space--lg btn-space--pill">Diskusikan Kebutuhan <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
                        <a href="#diagnosis" class="btn-space btn-space--lg btn-space--glass">Cek Kebutuhan Perusahaan <span class="btn-space__arrow" aria-hidden="true">&darr;</span></a>
                    </div>
                </div>

                <aside data-reveal-load style="--d: 160ms" class="lg:col-span-5 corp-ledger" aria-label="Biaya proses manual yang belum dihitung">
                    <p class="flex items-center justify-between gap-4 border-b border-white/15 pb-3 mb-1 font-mono text-[11px] uppercase tracking-[0.14em] text-white/60">
                        <span>Biaya proses manual</span>
                        <span>Per tahun</span>
                    </p>
                    <ul>
                        @foreach ($ledger as $row)
                            <li class="flex items-baseline justify-between gap-6 py-3.5 border-b border-white/10 text-white/80">
                                <span>{{ $row }}</span>
                                <span class="font-mono text-white/40 shrink-0" aria-label="belum dihitung">Rp ?</span>
                            </li>
                        @endforeach
                    </ul>
                    <p class="flex items-baseline justify-between gap-4 pt-4 mb-5">
                        <span class="font-display font-bold text-white text-lg">Total</span>
                        <span class="font-mono text-brand-300 text-right">Belum pernah dihitung</span>
                    </p>
                    <a href="#hitung-biaya" class="group inline-flex items-center gap-2 text-sm font-semibold text-brand-300 hover:text-brand-200">Hitung punya Anda <span class="transition-transform group-hover:translate-x-1" aria-hidden="true">&rarr;</span></a>
                </aside>
            </div>
        </div>
    </section>

    {{-- Who it is for: departments, deliberately not a client-logo strip --}}
    <section class="border-b border-white/10 bg-space-deep/60">
        <div class="max-w-[1320px] mx-auto px-4 md:px-8 py-5 flex flex-col lg:flex-row lg:items-center gap-3 lg:gap-8">
            <p class="text-sm text-white/60 shrink-0">Untuk setiap divisi yang masih bergantung pada spreadsheet:</p>
            <ul class="flex flex-wrap gap-x-6 gap-y-1.5">
                @foreach ($divisions as $division)
                    <li class="font-semibold text-white/85">{{ $division }}</li>
                @endforeach
            </ul>
        </div>
    </section>

    {{-- 01: findings, written like an audit --}}
    <section class="max-w-[1320px] mx-auto px-4 md:px-8 py-12 md:py-16">
        <x-public.corp-heading index="01" label="Temuan" title="Empat Biaya yang Tidak Pernah Muncul di Laporan Keuangan" />
        <ol class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach ($findings as $i => $finding)
                <li data-reveal class="flex flex-col rounded-sm border border-white/10 border-t-2 border-t-brand-400 bg-space-deep p-5 md:p-6">
                    <span class="font-mono text-sm text-brand-300 mb-3">Temuan {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <h3 class="font-display font-bold text-xl leading-snug text-white mb-2">{{ $finding['title'] }}</h3>
                    <p class="text-white/65 leading-relaxed mb-4">{{ $finding['text'] }}</p>
                    <p class="mt-auto rounded-sm bg-brand-400/15 px-4 py-3 text-white font-semibold leading-snug">{{ $finding['ask'] }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    {{-- Before/after: what the same report looks like today and with a system --}}
    <section class="border-y border-white/10 bg-space-deep/60 py-12 md:py-16">
        <div class="max-w-[1320px] mx-auto px-4 md:px-8">
            <x-public.corp-heading label="Sebelum dan Sesudah" title="Data yang Sama, Cara Kerja yang Berbeda" />
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                {{-- Today: scattered spreadsheet --}}
                <figure data-reveal class="rounded-sm border border-white/15 bg-space-void overflow-hidden flex flex-col">
                    <figcaption class="flex items-center justify-between gap-3 border-b border-white/15 px-4 py-3 text-sm">
                        <span class="font-semibold text-white">Hari ini</span>
                        <span class="font-mono text-xs text-white/55 truncate">laporan_final_revisi3 (2).xlsx</span>
                    </figcaption>
                    <div class="overflow-x-auto" aria-hidden="true">
                        <table class="corp-sheet w-full min-w-[420px] font-mono text-xs">
                            <tr><th>Cabang</th><th>Stok sistem</th><th>Stok gudang</th><th>Status</th></tr>
                            <tr><td>Jakarta</td><td>1.240</td><td class="is-warn">1.198</td><td class="is-warn">Selisih</td></tr>
                            <tr><td>Bandung</td><td>860</td><td>860</td><td>Cek ulang?</td></tr>
                            <tr><td>Surabaya</td><td class="is-warn">#REF!</td><td>532</td><td class="is-warn">Tanya Pak Budi</td></tr>
                            <tr><td>Medan</td><td>415</td><td class="is-warn">kosong</td><td class="is-warn">Belum dikirim</td></tr>
                            <tr><td>Makassar</td><td>298</td><td>301</td><td class="is-warn">Selisih</td></tr>
                        </table>
                    </div>
                    <ul class="grid grid-cols-1 sm:grid-cols-3 gap-px bg-white/10 border-t border-white/15 mt-auto text-sm text-white/70">
                        <li class="bg-space-void px-4 py-3">Direkap manual tiap akhir bulan</li>
                        <li class="bg-space-void px-4 py-3">Beberapa versi file beredar</li>
                        <li class="bg-space-void px-4 py-3">Bergantung pada satu orang</li>
                    </ul>
                </figure>

                {{-- With a system: one live dashboard --}}
                <figure data-reveal data-reveal-delay="80" class="rounded-sm border border-brand-400/60 bg-space-void overflow-hidden flex flex-col">
                    <figcaption class="flex items-center justify-between gap-3 border-b border-white/15 px-4 py-3 text-sm">
                        <span class="font-semibold text-white">Dengan sistem</span>
                        <span class="flex items-center gap-2 font-mono text-xs text-brand-300"><span class="w-1.5 h-1.5 rounded-full bg-brand-400"></span>Data terkini</span>
                    </figcaption>
                    <div class="p-4 flex-1" aria-hidden="true">
                        <div class="grid grid-cols-3 gap-2 mb-4">
                            <div class="rounded-sm bg-space-panel p-3"><p class="text-[11px] text-white/60 mb-1">Total stok</p><p class="font-mono text-lg text-white">3.345</p></div>
                            <div class="rounded-sm bg-space-panel p-3"><p class="text-[11px] text-white/60 mb-1">Perlu dicek</p><p class="font-mono text-lg text-brand-300">2</p></div>
                            <div class="rounded-sm bg-space-panel p-3"><p class="text-[11px] text-white/60 mb-1">Menunggu ACC</p><p class="font-mono text-lg text-white">4</p></div>
                        </div>
                        <div class="flex items-end gap-2 h-24 border-b border-white/15">
                            @foreach ([['Jkt', 92], ['Bdg', 64], ['Sby', 40], ['Mdn', 31], ['Mks', 22]] as [$city, $bar])
                                <div class="flex-1 h-full flex flex-col justify-end items-center gap-1">
                                    <span class="w-full rounded-t-sm bg-brand-400" style="height: {{ $bar }}%"></span>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex gap-2 pt-1 font-mono text-[11px] text-white/55 text-center">
                            @foreach (['Jkt', 'Bdg', 'Sby', 'Mdn', 'Mks'] as $city)<span class="flex-1">{{ $city }}</span>@endforeach
                        </div>
                    </div>
                    <ul class="grid grid-cols-1 sm:grid-cols-3 gap-px bg-white/10 border-t border-white/15 mt-auto text-sm text-white/85">
                        <li class="bg-space-void px-4 py-3">Satu sumber data</li>
                        <li class="bg-space-void px-4 py-3">Selisih langsung ditandai</li>
                        <li class="bg-space-void px-4 py-3">Bisa diakses sesuai peran</li>
                    </ul>
                </figure>
            </div>
            <p class="mt-3 text-xs text-white/50">Contoh tampilan dengan data ilustrasi, bukan data klien.</p>
        </div>
    </section>

    {{-- 02: self-diagnosis; the verdict can honestly say "not yet" --}}
    <section id="diagnosis" class="force-dark corp-band scroll-mt-20 py-12 md:py-16 text-white">
        <div class="max-w-[1320px] mx-auto px-4 md:px-8">
            <x-public.corp-heading index="02" label="Diagnosis" title="Apakah Perusahaan Anda Sudah Membutuhkannya?" />
            <div data-need-check class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-start">
                <fieldset class="lg:col-span-7">
                    <legend class="text-white/80 mb-5">Centang yang terjadi di perusahaan Anda saat ini.</legend>
                    <div class="border-t border-white/25">
                        @foreach ($checks as $check)
                            <label class="flex items-start gap-4 border-b border-white/25 py-4 cursor-pointer transition-colors hover:bg-white/5">
                                <input type="checkbox" class="corp-check mt-0.5 shrink-0">
                                <span class="text-white/90 md:text-lg leading-snug">{{ $check }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
                <div class="lg:col-span-5 lg:sticky lg:top-24 bg-space-void p-6 md:p-8 rounded-sm" aria-live="polite">
                    <p class="flex items-baseline justify-between font-mono text-[11px] uppercase tracking-[0.14em] text-white/60 mb-3">
                        <span>Hasil</span>
                        <span><span data-need-count class="text-white">0</span> dari {{ count($checks) }}</span>
                    </p>
                    <div class="h-1 bg-white/15 mb-6" aria-hidden="true"><div data-need-bar class="h-full bg-brand-400 transition-all duration-300" style="width: 0%"></div></div>
                    <div data-need-verdict="0">
                        <p class="font-display font-bold text-2xl text-white mb-2">Belum terlihat mendesak</p>
                        <p class="text-white/70 leading-relaxed">Bila hanya sedikit yang terasa, aplikasi yang ada mungkin masih cukup. Centang kondisi yang sesuai untuk melihat hasilnya.</p>
                    </div>
                    <div data-need-verdict="1" class="hidden">
                        <p class="font-display font-bold text-2xl text-white mb-2">Layak dievaluasi</p>
                        <p class="text-white/70 leading-relaxed">Hambatannya mulai berulang. Sekarang saat yang baik untuk menghitung biayanya, sebelum masalah ini membesar bersama bisnis Anda.</p>
                    </div>
                    <div data-need-verdict="2" class="hidden">
                        <p class="font-display font-bold text-2xl text-white mb-2">Proses Anda sudah membebani bisnis</p>
                        <p class="text-white/70 leading-relaxed">Kondisi ini biasanya tidak membaik dengan sendirinya. Saat tim dan transaksi bertambah, bebannya ikut bertambah. Sebaiknya segera dipetakan.</p>
                    </div>
                    <a href="{{ $consultUrl }}" class="btn-space btn-space--pill mt-6">Bahas Hasil Ini <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
                </div>
            </div>
        </div>
    </section>

    {{-- 03: cost of waiting, driven only by the visitor's own numbers --}}
    <section id="hitung-biaya" class="scroll-mt-20 max-w-[1320px] mx-auto px-4 md:px-8 py-12 md:py-16">
        <x-public.corp-heading index="03" label="Biaya Menunda" title="Setiap Bulan Menunda Ada Harganya. Berapa Harga Anda?" />
        <div data-roi-calc data-reveal class="grid grid-cols-1 lg:grid-cols-12 border border-white/15 rounded-sm">
            <div class="lg:col-span-5 p-6 md:p-8 space-y-5 lg:border-r border-white/15">
                <label class="block">
                    <span class="block text-sm font-semibold text-white/80 mb-2">Jumlah staf terlibat</span>
                    <input data-roi-staff type="number" min="1" max="10000" value="5" inputmode="numeric" class="field-input !pl-4 !rounded-sm w-full font-mono">
                </label>
                <label class="block">
                    <span class="block text-sm font-semibold text-white/80 mb-2">Jam kerja manual per hari, per orang</span>
                    <input data-roi-hours type="number" min="0" max="24" step="0.5" value="2" inputmode="decimal" class="field-input !pl-4 !rounded-sm w-full font-mono">
                </label>
                <label class="block">
                    <span class="block text-sm font-semibold text-white/80 mb-2">Biaya per jam (Rp)</span>
                    <input data-roi-rate type="number" min="0" step="5000" value="50000" inputmode="numeric" class="field-input !pl-4 !rounded-sm w-full font-mono">
                </label>
            </div>
            <div class="lg:col-span-7 p-6 md:p-8 flex flex-col border-t lg:border-t-0 border-white/15" aria-live="polite">
                <p class="text-sm font-semibold text-white/70 mb-2">Biaya setiap bulan menunda</p>
                <p data-roi-monthly class="font-mono font-medium text-3xl sm:text-4xl xl:text-5xl tracking-tight text-brand-300 [overflow-wrap:anywhere] mb-6">Rp11.000.000</p>
                <dl class="border-t border-white/15 mb-6">
                    <div class="flex items-baseline justify-between gap-4 py-3 border-b border-white/10">
                        <dt class="text-white/70">Per tahun</dt>
                        <dd data-roi-yearly class="font-mono text-white text-right [overflow-wrap:anywhere]">Rp132.000.000</dd>
                    </div>
                    <div class="flex items-baseline justify-between gap-4 py-3 border-b border-white/10">
                        <dt class="text-white/70">Jam kerja per bulan (22 hari)</dt>
                        <dd data-roi-monthly-hours class="font-mono text-white">220</dd>
                    </div>
                </dl>
                <p class="text-sm text-white/55 leading-relaxed mb-6">Ini biaya proses saat ini, bukan janji penghematan. Tidak semua jam bisa diotomasi; berapa yang realistis kita hitung bersama.</p>
                <div class="mt-auto"><a href="{{ $consultUrl }}" class="btn-space btn-space--pill">Hitung Bersama Kami <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a></div>
            </div>
        </div>
    </section>

    {{-- 04: solutions per department, as a matrix --}}
    <section class="max-w-[1320px] mx-auto px-4 md:px-8 pb-12 md:pb-16">
        <x-public.corp-heading index="04" label="Solusi" title="Satu Sistem untuk Setiap Divisi yang Membutuhkan" />
        <ul class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
            @foreach ($solutions as $row)
                <li data-reveal class="flex flex-col rounded-sm border border-white/10 bg-space-deep overflow-hidden">
                    <div class="bg-brand-400/15 px-5 py-4 border-b border-white/10">
                        <p class="font-mono text-[11px] uppercase tracking-[0.1em] text-brand-300 mb-1">{{ $row['unit'] }}</p>
                        <p class="font-display font-bold text-lg leading-snug text-white">{{ $row['system'] }}</p>
                    </div>
                    <p class="px-5 py-4 text-sm text-white/65 leading-relaxed"><span class="block font-semibold text-white/85 mb-1">Kondisi hari ini</span>{{ $row['today'] }}</p>
                </li>
            @endforeach
        </ul>
        <p data-reveal class="mt-7 text-white/65 max-w-3xl">Kebutuhan Anda tidak ada di daftar? Lihat <a href="/blog/contoh-software-custom-perusahaan" class="text-brand-300 underline underline-offset-4 hover:text-brand-200">contoh software custom</a> dan <a href="/blog/pembuatan-aplikasi-perusahaan" class="text-brand-300 underline underline-offset-4 hover:text-brand-200">cara memilih platform aplikasi</a>.</p>
    </section>

    {{-- 05: pricing is handled in conversation, so this block only routes to contact --}}
    <section id="harga" class="force-dark corp-band relative overflow-hidden scroll-mt-20 text-white">
        <img src="/images/software/server-rack.webp" alt="" aria-hidden="true" width="1280" height="852" loading="lazy" class="absolute inset-y-0 right-0 w-full lg:w-1/2 h-full object-cover opacity-40">
        <div class="absolute inset-0 bg-gradient-to-r from-[#0b2a6f] from-50% to-[#0b2a6f]/40" aria-hidden="true"></div>
        <div class="relative max-w-[1320px] mx-auto px-4 md:px-8 py-10 md:py-12 grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-center">
            <div class="lg:col-span-7">
                <p class="font-mono text-[11px] uppercase tracking-[0.14em] text-white/70 mb-4">05 &nbsp; Harga</p>
                <h2 class="font-display font-bold text-2xl md:text-4xl leading-[1.12] text-white mb-4 [text-wrap:balance]">Harga Disesuaikan dengan Kebutuhan Perusahaan Anda</h2>
                <p class="text-white/80 md:text-lg leading-relaxed max-w-2xl">Hubungi kami dan ceritakan kebutuhannya. Tim kami akan menyiapkan estimasi yang sesuai untuk perusahaan Anda.</p>
            </div>
            <div class="lg:col-span-5 flex flex-col sm:flex-row lg:flex-col xl:flex-row gap-3 lg:justify-end">
                <a href="/kontak" class="btn-space btn-space--lg btn-space--pill">Hubungi Kami <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
                @if ($whatsappUrl)
                    <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="btn-space btn-space--lg btn-space--glass">Chat WhatsApp <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
                @endif
            </div>
        </div>
    </section>

    {{-- 06: honest comparison --}}
    <section class="max-w-[1320px] mx-auto px-4 md:px-8 py-12 md:py-16">
        <x-public.corp-heading index="06" label="Perbandingan" title="Tiga Pilihan, dan Kapan Masing-Masing Tepat" />
        <div data-reveal class="overflow-x-auto" tabindex="0" role="region" aria-label="Tabel perbandingan pilihan software">
            <table class="w-full min-w-[720px] text-left border-collapse">
                <thead>
                    <tr>
                        <th scope="col" class="w-[19%] py-4 pr-4 font-mono text-[11px] font-normal uppercase tracking-[0.14em] text-white/55 align-bottom">Kriteria</th>
                        <th scope="col" class="w-[27%] p-4 font-display font-bold text-lg text-white align-bottom">Langganan aplikasi</th>
                        <th scope="col" class="w-[27%] p-4 bg-brand-400/15 border-t-2 border-brand-400 align-bottom">
                            <span class="block font-mono text-[11px] font-normal uppercase tracking-[0.1em] text-brand-300 mb-1">Bersama ExodineWeb</span>
                            <span class="font-display font-bold text-lg text-white">Software custom</span>
                        </th>
                        <th scope="col" class="w-[27%] p-4 font-display font-bold text-lg text-white align-bottom">Tim internal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($comparison as $row)
                        <tr class="border-t border-white/15">
                            <th scope="row" class="py-4 pr-4 text-sm font-semibold text-white/70 align-top">{{ $row['label'] }}</th>
                            <td class="p-4 text-white/65 align-top">{{ $row['saas'] }}</td>
                            <td class="p-4 text-white font-semibold bg-brand-400/15 align-top">{{ $row['custom'] }}</td>
                            <td class="p-4 text-white/65 align-top">{{ $row['team'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <p data-reveal class="mt-7 text-white/65 max-w-3xl">Kalau aplikasi yang sudah ada ternyata cukup untuk kebutuhan Anda, kami akan menyampaikannya saat diskusi awal.</p>
    </section>

    {{-- 07: process, each step with its deliverable --}}
    <section class="border-y border-white/10 bg-space-deep/60 py-12 md:py-16">
        <div class="max-w-[1320px] mx-auto px-4 md:px-8">
            <x-public.corp-heading index="07" label="Cara Kerja" title="Setiap Tahap Punya Hasil yang Bisa Anda Periksa" />
            <ol class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-px bg-white/15 border border-white/15">
                @foreach ($process as $i => $step)
                    <li data-reveal class="bg-space-void p-5 flex flex-col min-h-[12rem]">
                        <span class="font-mono text-sm text-brand-300 mb-6">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="font-display font-bold text-lg leading-snug text-white mb-3">{{ $step['title'] }}</h3>
                        <p class="text-sm text-white/60 leading-relaxed mt-auto">{{ $step['output'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- 08: what each internal approver wants to read --}}
    <section class="max-w-[1320px] mx-auto px-4 md:px-8 py-12 md:py-16">
        <x-public.corp-heading index="08" label="Untuk Tim Internal Anda" title="Jawaban untuk Setiap Pihak yang Ikut Memutuskan" />
        <img src="/images/software/ruang-server.webp" alt="Deretan rak server di ruang data perusahaan" width="1280" height="853" loading="lazy" class="w-full h-44 md:h-56 object-cover rounded-sm mb-5">
        <dl class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach ($stakeholders as $item)
                <div data-reveal class="rounded-sm border border-white/10 border-t-2 border-t-brand-400 bg-space-deep p-5">
                    <p class="font-mono text-[11px] uppercase tracking-[0.14em] text-brand-300 mb-3">{{ $item['who'] }}</p>
                    <dt class="font-display font-bold text-xl leading-snug text-white mb-3">{{ $item['question'] }}</dt>
                    <dd class="text-white/65 leading-relaxed">{{ $item['answer'] }}</dd>
                </div>
            @endforeach
        </dl>
    </section>

    @if (! empty($service->faqs))
        <section class="max-w-[1320px] mx-auto px-4 md:px-8 pb-12 md:pb-16">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-16">
                <div class="lg:col-span-4">
                    <x-public.corp-heading index="09" label="FAQ" title="Pertanyaan Umum" class="!mb-0" />
                </div>
                <div class="lg:col-span-8 border-t border-white/15">
                    @foreach ($service->faqs as $faq)
                        <details class="group border-b border-white/15">
                            <summary class="flex items-start justify-between gap-6 py-5 cursor-pointer list-none font-display font-bold text-lg text-white [&::-webkit-details-marker]:hidden">
                                {{ $faq['question'] }}
                                <span class="text-brand-300 text-xl leading-none transition-transform duration-200 group-open:rotate-45" aria-hidden="true">+</span>
                            </summary>
                            <p class="pb-6 text-white/65 leading-relaxed max-w-2xl">{{ $faq['answer'] }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($relatedPosts->isNotEmpty())
        <section class="max-w-[1320px] mx-auto px-4 md:px-8 pb-12 md:pb-16">
            <x-public.corp-heading index="10" label="Panduan" title="Bahan untuk Rapat Internal Anda" />
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach ($relatedPosts as $post)
                    <a href="/blog/{{ $post->slug }}" data-reveal class="group block">
                        <div class="aspect-[16/9] rounded-sm overflow-hidden bg-space-panel mb-4">
                            @if ($post->featured_image)
                                <img src="{{ \App\Support\Img::url($post->featured_image, 960) }}" srcset="{{ \App\Support\Img::srcset($post->featured_image, [480, 960]) }}" sizes="(min-width: 768px) 33vw, 100vw" alt="{{ $post->featured_image_alt ?: $post->title }}" width="640" height="360" loading="lazy" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-[1.03]">
                            @endif
                        </div>
                        <h3 class="font-display font-bold text-xl leading-snug text-white mb-2 transition-colors group-hover:text-brand-300">{{ $post->title }}</h3>
                        <p class="text-sm text-white/60 leading-relaxed">{{ $post->excerpt }}</p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    <x-public.contact-cta
        form-id="custom-software-cta-form"
        eyebrow="Mulai dari Diskusi"
        title="Ceritakan Proses yang Paling Menguras Waktu Tim Anda"
        description="Diskusi awal gratis dan tanpa kewajiban. Anda akan mendapat gambaran solusi dan langkah berikutnya, meski akhirnya memutuskan tidak membuat software."
        source="konsultasi"
        service-interested="Software Custom"
    />
</x-layouts.public>
