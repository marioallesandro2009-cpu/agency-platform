@php
    $breadcrumbItems = [
        ['label' => 'Home', 'href' => '/'],
        ['label' => 'Jasa & Layanan', 'href' => '/jasa-website'],
        ['label' => $service->name],
    ];
    $consultUrl = '/konsultasi?service=' . rawurlencode('Software Custom');

    $signs = [
        'Laporan bulanan butuh berhari-hari karena data harus digabung manual dari banyak Excel.',
        'Tim memakai beberapa aplikasi yang tidak saling terhubung, sehingga data diinput berulang.',
        'Biaya lisensi per pengguna terus naik setiap kali tim bertambah.',
        'Kesalahan input menimbulkan selisih stok, salah tagih, atau keluhan pelanggan yang berulang.',
        'Aplikasi siap pakai memaksa tim mengubah alur kerja yang selama ini sudah terbukti berjalan.',
        'Ada proses penting yang hanya dipahami satu orang dan tidak terdokumentasi.',
    ];

    $examples = [
        ['code' => 'ERP', 'title' => 'ERP Sesuai Alur Anda', 'problem' => 'Data pembelian, penjualan, stok, dan keuangan tersebar di banyak tempat.', 'result' => 'Satu sistem dengan modul yang benar-benar Anda pakai.'],
        ['code' => 'HR', 'title' => 'HRIS & Payroll', 'problem' => 'Rekap absensi, cuti, lembur, dan gaji menyita waktu tim HR setiap bulan.', 'result' => 'Perhitungan otomatis mengikuti aturan perusahaan Anda.'],
        ['code' => 'CRM', 'title' => 'CRM & Pipeline Sales', 'problem' => 'Prospek hilang karena follow-up dicatat di chat dan catatan pribadi.', 'result' => 'Setiap prospek, penawaran, dan tindak lanjut tercatat rapi.'],
        ['code' => 'INV', 'title' => 'Inventory & Gudang', 'problem' => 'Stok di catatan berbeda dengan stok di rak, apalagi di banyak gudang.', 'result' => 'Barcode, stok real-time, dan peringatan stok minimum.'],
        ['code' => 'APP', 'title' => 'Aplikasi Mobile', 'problem' => 'Sales, teknisi, dan kurir baru bisa melapor setelah kembali ke kantor.', 'result' => 'Data lapangan masuk langsung dari ponsel Android dan iOS.'],
        ['code' => 'PRT', 'title' => 'Portal Klien & Vendor', 'problem' => 'Tim terus menjawab pertanyaan status pesanan dan permintaan dokumen.', 'result' => 'Klien memantau status dan mengunduh dokumen sendiri.'],
        ['code' => 'WFL', 'title' => 'Approval & E-Office', 'problem' => 'Pengajuan dana dan pembelian tertahan karena menunggu tanda tangan.', 'result' => 'Persetujuan berjenjang yang bisa dilacak dari mana saja.'],
        ['code' => 'BI', 'title' => 'Dashboard & Integrasi', 'problem' => 'Manajemen menunggu laporan, dan data dari tiap sistem tidak nyambung.', 'result' => 'Angka penting terkumpul otomatis dalam satu dashboard.'],
    ];

    $priceFactors = [
        ['title' => 'Jumlah proses dan peran', 'text' => 'Semakin banyak alur persetujuan dan jenis pengguna, semakin banyak yang dibangun dan diuji.'],
        ['title' => 'Integrasi', 'text' => 'Menghubungkan sistem lama, payment gateway, atau API pihak ketiga.'],
        ['title' => 'Migrasi data', 'text' => 'Membersihkan dan memindahkan data yang sudah Anda miliki.'],
        ['title' => 'Platform', 'text' => 'Aplikasi web, PWA, Android, iOS, atau gabungannya.'],
    ];

    $priceWays = [
        ['title' => 'Bangun yang berdampak dulu', 'text' => 'Mulai dari modul yang paling terasa manfaatnya. Tahap berikutnya diputuskan dari pemakaian nyata, bukan asumsi.'],
        ['title' => 'Estimasi tertulis per modul', 'text' => 'Anda melihat rincian biaya, asumsi, dan hal yang tidak termasuk, sehingga mudah dibandingkan dengan vendor lain.'],
        ['title' => 'Bayar per milestone', 'text' => 'Pembayaran terhubung dengan hasil yang bisa Anda lihat dan uji, bukan sekadar janji di akhir proyek.'],
        ['title' => 'Tanpa biaya per pengguna', 'text' => 'Tim bertambah tidak otomatis menambah biaya lisensi, karena sistemnya dibuat untuk perusahaan Anda.'],
    ];

    $options = [
        [
            'name' => 'Langganan aplikasi',
            'tag' => 'Siap pakai',
            'best' => 'Kebutuhan umum yang tidak membedakan bisnis Anda.',
            'pros' => ['Cepat dimulai', 'Biaya awal rendah'],
            'cons' => ['Biaya terus berjalan, sering mengikuti jumlah pengguna', 'Anda yang menyesuaikan diri dengan fiturnya'],
            'highlight' => false,
        ],
        [
            'name' => 'Software custom',
            'tag' => 'Bersama ExodineWeb',
            'best' => 'Proses khas yang menentukan cara bisnis Anda berjalan.',
            'pros' => ['Mengikuti alur kerja Anda', 'Bisa dibangun bertahap sesuai anggaran', 'Tanpa biaya lisensi per pengguna'],
            'cons' => ['Perlu investasi awal dan waktu pengerjaan'],
            'highlight' => true,
        ],
        [
            'name' => 'Tim internal',
            'tag' => 'Rekrut sendiri',
            'best' => 'Software adalah inti bisnis dan dikembangkan terus-menerus.',
            'pros' => ['Kendali penuh', 'Tim selalu tersedia'],
            'cons' => ['Beban gaji tetap dan proses rekrutmen', 'Risiko saat anggota tim keluar'],
            'highlight' => false,
        ],
    ];

    $process = [
        ['title' => 'Diskusi Kebutuhan', 'text' => 'Gratis. Kami memahami masalah, alur kerja, dan target Anda. NDA bisa ditandatangani lebih dulu.'],
        ['title' => 'Pemetaan & Estimasi', 'text' => 'Kebutuhan dipetakan menjadi modul, lalu Anda menerima estimasi tertulis, jadwal, dan asumsinya.'],
        ['title' => 'Rancangan Sistem', 'text' => 'Alur dan tampilan disepakati bersama sebelum kode ditulis, saat perubahan masih murah.'],
        ['title' => 'Pengembangan Bertahap', 'text' => 'Setiap tahap bisa Anda lihat dan coba. Masukan diterapkan sebelum melangkah lebih jauh.'],
        ['title' => 'Uji Coba & Migrasi', 'text' => 'Tim Anda menguji dengan skenario nyata, lalu data lama dipindahkan dengan terencana.'],
        ['title' => 'Peluncuran & Pendampingan', 'text' => 'Pelatihan pengguna, dokumentasi, dan dukungan setelah sistem mulai dipakai.'],
    ];

    $assurances = [
        ['title' => 'Kerahasiaan', 'text' => 'Bersedia menandatangani NDA sebelum diskusi detail.'],
        ['title' => 'Kepemilikan Jelas', 'text' => 'Source code, dokumentasi, dan akses diatur tertulis di kontrak.'],
        ['title' => 'Keamanan Sejak Rancangan', 'text' => 'Hak akses per peran, pencatatan aktivitas, dan backup masuk dalam desain.'],
        ['title' => 'Tidak Terkunci', 'text' => 'Dibangun dengan teknologi umum, sehingga tetap bisa dikembangkan tim lain.'],
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
    <x-public.space-page-header
        eyebrow="Software Custom"
        code="SW"
        title="Jasa Pembuatan Software Custom yang Mengikuti Cara Bisnis Anda"
        subtitle="Sistem internal, integrasi, dan aplikasi mobile yang dirancang dari proses perusahaan Anda. Anda hanya membayar yang benar-benar dipakai, dan bisa membangunnya bertahap."
        :breadcrumb-items="$breadcrumbItems"
    >
        <div class="flex flex-wrap gap-3">
            <a href="{{ $consultUrl }}" class="btn-space btn-space--lg btn-space--pill">Diskusikan Kebutuhan <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
            <a href="#hitung-biaya" class="btn-space btn-space--lg btn-space--glass">Hitung Biaya Proses Manual <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
        </div>
    </x-public.space-page-header>

    {{-- 01: the one idea of the page, then the signs a visitor recognises --}}
    <section class="max-w-[1440px] mx-auto px-4 md:px-8 py-24 md:py-32">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16">
            <div class="lg:col-span-5">
                <x-public.space-heading index="01" label="Masalahnya" title="Bisnis Anda Tidak Sama, Kenapa Software-nya Harus Sama?" class="!mb-8" />
                <p data-reveal class="text-white/60 leading-relaxed md:text-lg">Aplikasi siap pakai dibuat untuk rata-rata semua perusahaan. Software custom dibuat untuk satu perusahaan: milik Anda. Bila salah satu tanda ini terasa akrab, proses Anda mungkin sudah layak mendapat sistemnya sendiri.</p>
            </div>
            <ol class="lg:col-span-7 border-t border-white/10">
                @foreach ($signs as $i => $sign)
                    <li data-reveal data-reveal-delay="{{ min($i * 60, 240) }}" class="flex gap-5 border-b border-white/10 py-5 md:py-6">
                        <span class="font-mono text-[11px] tracking-[0.2em] text-brand-400 pt-1.5" aria-hidden="true">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <p class="text-white/80 md:text-lg leading-relaxed">{{ $sign }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- 02: examples, written as problem then result --}}
    <section class="max-w-[1440px] mx-auto px-4 md:px-8 pb-24 md:pb-32">
        <x-public.space-heading index="02" label="Contoh Solusi" title="Apa Saja yang Bisa Kami Bangun" />
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-px rounded-2xl overflow-hidden border border-white/10 bg-white/10">
            @foreach ($examples as $i => $example)
                <article data-reveal data-reveal-delay="{{ min($i * 50, 200) }}" class="bg-space-void p-6 md:p-7 flex flex-col">
                    <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400 mb-8">/ {{ $example['code'] }}</p>
                    <h3 class="font-display font-semibold text-xl text-white tracking-tight mb-3">{{ $example['title'] }}</h3>
                    <p class="text-sm text-white/50 leading-relaxed mb-4">{{ $example['problem'] }}</p>
                    <p class="text-sm text-white/85 leading-relaxed mt-auto pt-4 border-t border-white/10">{{ $example['result'] }}</p>
                </article>
            @endforeach
        </div>
        <p data-reveal class="mt-8 text-white/60 max-w-3xl">Kebutuhan Anda tidak ada di daftar? Justru itu gunanya software custom. Baca <a href="/blog/contoh-software-custom-perusahaan" class="text-brand-300 underline underline-offset-4 hover:text-brand-200">contoh dan kapan perusahaan membutuhkannya</a>, atau <a href="/blog/pembuatan-aplikasi-perusahaan" class="text-brand-300 underline underline-offset-4 hover:text-brand-200">cara memilih antara aplikasi web dan mobile</a>.</p>
    </section>

    {{-- 03: why there is no sticker price, and how the cost stays efficient --}}
    <section class="relative py-24 md:py-32 border-y border-white/10 bg-space-deep/50">
        <div class="max-w-[1440px] mx-auto px-4 md:px-8">
            <x-public.space-heading index="03" label="Soal Harga" title="Kenapa Kami Tidak Menembak Harga, dan Kenapa Itu Menguntungkan Anda" />
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16">
                <div class="lg:col-span-5">
                    <p data-reveal class="text-white/70 leading-relaxed md:text-lg mb-8">Harga yang diberikan sebelum memahami kebutuhan hanyalah tebakan. Risikonya kembali kepada Anda: fitur dipangkas di tengah jalan atau tagihan tambahan. Biaya ditentukan oleh empat hal ini:</p>
                    <dl class="border-t border-white/10">
                        @foreach ($priceFactors as $factor)
                            <div data-reveal class="py-4 border-b border-white/10">
                                <dt class="font-display font-semibold text-white">{{ $factor['title'] }}</dt>
                                <dd class="text-sm text-white/55 mt-1">{{ $factor['text'] }}</dd>
                            </div>
                        @endforeach
                    </dl>
                </div>
                <div class="lg:col-span-7">
                    <p data-reveal class="font-mono text-[10px] md:text-[11px] uppercase tracking-[0.25em] text-brand-400 mb-6">Cara kami menjaga biaya tetap efisien</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach ($priceWays as $i => $way)
                            <div data-reveal data-reveal-delay="{{ $i * 60 }}" class="rounded-2xl border border-white/10 bg-space-void/70 p-6">
                                <h3 class="font-display font-semibold text-lg text-white tracking-tight mb-2">{{ $way['title'] }}</h3>
                                <p class="text-sm text-white/60 leading-relaxed">{{ $way['text'] }}</p>
                            </div>
                        @endforeach
                    </div>
                    <p data-reveal class="mt-6 text-white/60">Silakan bandingkan estimasi kami dengan vendor lain. Panduan <a href="/blog/biaya-pembuatan-software-custom" class="text-brand-300 underline underline-offset-4 hover:text-brand-200">biaya pembuatan software</a> menjelaskan cara membacanya dengan adil.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- 04: value calculator, driven only by the visitor's own numbers --}}
    <section id="hitung-biaya" class="scroll-mt-24 max-w-[1440px] mx-auto px-4 md:px-8 py-24 md:py-32">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-start">
            <div class="lg:col-span-5">
                <x-public.space-heading index="04" label="Hitung Sendiri" title="Berapa Harga Proses Manual Anda?" class="!mb-8" />
                <p data-reveal class="text-white/60 leading-relaxed md:text-lg">Sebelum menilai mahal atau murah, lihat dulu berapa yang sudah Anda bayar setiap tahun untuk pekerjaan yang bisa dibantu sistem. Masukkan angka perusahaan Anda sendiri.</p>
            </div>
            <div data-roi-calc data-reveal class="lg:col-span-7 rounded-3xl border border-brand-400/30 bg-space-deep/70 p-6 md:p-10">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
                    <label class="block">
                        <span class="block text-sm text-white/70 mb-2">Jumlah staf terlibat</span>
                        <input data-roi-staff type="number" min="1" max="10000" value="5" inputmode="numeric" class="field-input !pl-4 w-full">
                    </label>
                    <label class="block">
                        <span class="block text-sm text-white/70 mb-2">Jam manual per hari</span>
                        <input data-roi-hours type="number" min="0" max="24" step="0.5" value="2" inputmode="decimal" class="field-input !pl-4 w-full">
                    </label>
                    <label class="block">
                        <span class="block text-sm text-white/70 mb-2">Biaya per jam (Rp)</span>
                        <input data-roi-rate type="number" min="0" step="5000" value="50000" inputmode="numeric" class="field-input !pl-4 w-full">
                    </label>
                </div>
                <div class="border-t border-white/10 pt-7" aria-live="polite">
                    <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-white/50 mb-2">Biaya proses manual per tahun</p>
                    <p data-roi-yearly class="font-display font-semibold text-3xl sm:text-4xl xl:text-5xl text-brand-300 tracking-tight tabular-nums [overflow-wrap:anywhere]">Rp132.000.000</p>
                    <p class="text-sm text-white/55 mt-3"><span data-roi-monthly-hours>220</span> jam kerja per bulan, dengan asumsi 22 hari kerja.</p>
                </div>
                <p class="text-xs text-white/45 leading-relaxed mt-6">Ini biaya proses saat ini, bukan janji penghematan. Tidak semua jam bisa diotomasi. Berapa yang realistis, itulah yang kita hitung bersama saat diskusi kebutuhan.</p>
                <a href="{{ $consultUrl }}" class="btn-space btn-space--pill mt-6">Hitung Bersama Kami <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
            </div>
        </div>
    </section>

    {{-- 05: honest comparison, including when custom is the wrong call --}}
    <section class="max-w-[1440px] mx-auto px-4 md:px-8 pb-24 md:pb-32">
        <x-public.space-heading index="05" label="Perbandingan" title="Software Custom Bukan Selalu Jawabannya" />
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:items-stretch">
            @foreach ($options as $i => $option)
                <article data-reveal data-reveal-delay="{{ $i * 80 }}" class="rounded-2xl border p-6 md:p-8 flex flex-col {{ $option['highlight'] ? 'border-brand-400/60 bg-brand-400/10 lg:-my-4 lg:py-12' : 'border-white/10 bg-space-deep/50' }}">
                    <p class="font-mono text-[10px] uppercase tracking-[0.25em] {{ $option['highlight'] ? 'text-brand-300' : 'text-white/45' }} mb-3">{{ $option['tag'] }}</p>
                    <h3 class="font-display font-semibold text-2xl text-white tracking-tight mb-3">{{ $option['name'] }}</h3>
                    <p class="text-sm text-white/60 leading-relaxed mb-6"><span class="text-white/85">Tepat untuk:</span> {{ $option['best'] }}</p>
                    <ul class="space-y-2.5 text-sm mb-5">
                        @foreach ($option['pros'] as $pro)
                            <li class="flex gap-3 text-white/85"><span class="text-brand-400" aria-hidden="true">+</span><span>{{ $pro }}</span></li>
                        @endforeach
                    </ul>
                    <ul class="space-y-2.5 text-sm pt-5 border-t border-white/10 mt-auto">
                        @foreach ($option['cons'] as $con)
                            <li class="flex gap-3 text-white/55"><span aria-hidden="true">&minus;</span><span>{{ $con }}</span></li>
                        @endforeach
                    </ul>
                </article>
            @endforeach
        </div>
        <p data-reveal class="mt-10 text-white/60 max-w-3xl">Kalau aplikasi yang sudah ada ternyata cukup untuk kebutuhan Anda, kami akan menyampaikannya saat diskusi awal.</p>
    </section>

    {{-- 06: process --}}
    <section class="relative py-24 md:py-32 border-y border-white/10 bg-space-deep/50">
        <div class="max-w-[1440px] mx-auto px-4 md:px-8">
            <x-public.space-heading index="06" label="Cara Kerja" title="Tanpa Kejutan dari Diskusi Pertama sampai Peluncuran" />
            <ol class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-10 gap-y-10">
                @foreach ($process as $i => $step)
                    <li data-reveal data-reveal-delay="{{ min($i * 60, 240) }}" class="border-t border-brand-400/40 pt-5">
                        <span class="font-display font-extrabold text-4xl text-transparent [-webkit-text-stroke:1px_rgb(var(--lime-400)/0.8)] tabular-nums" aria-hidden="true">{{ $i + 1 }}</span>
                        <h3 class="font-display font-semibold text-xl text-white tracking-tight mt-3 mb-2">{{ $step['title'] }}</h3>
                        <p class="text-sm text-white/60 leading-relaxed">{{ $step['text'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- 07: what procurement and IT want to read --}}
    <section class="max-w-[1440px] mx-auto px-4 md:px-8 py-24 md:py-32">
        <x-public.space-heading index="07" label="Untuk Korporat" title="Yang Biasanya Ditanyakan Legal, IT, dan Procurement" />
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-12 border-t border-white/10">
            @foreach ($assurances as $item)
                <div data-reveal class="py-6 border-b border-white/10">
                    <dt class="font-display font-semibold text-xl text-white tracking-tight mb-2">{{ $item['title'] }}</dt>
                    <dd class="text-white/60 leading-relaxed">{{ $item['text'] }}</dd>
                </div>
            @endforeach
        </dl>
    </section>

    @if (! empty($service->faqs))
        <section class="max-w-[1440px] mx-auto px-4 md:px-8 pb-24 md:pb-32">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16">
                <div class="lg:col-span-4">
                    <x-public.space-heading index="08" label="FAQ" title="Pertanyaan Umum" class="!mb-0" />
                </div>
                <div class="lg:col-span-8 border-t border-white/10">
                    @foreach ($service->faqs as $faq)
                        <details class="group border-b border-white/10">
                            <summary class="flex items-start justify-between gap-6 py-5 cursor-pointer list-none font-display font-semibold text-lg text-white tracking-tight [&::-webkit-details-marker]:hidden">
                                {{ $faq['question'] }}
                                <span class="text-brand-400 text-xl leading-none transition-transform duration-200 group-open:rotate-45" aria-hidden="true">+</span>
                            </summary>
                            <p class="pb-6 text-white/60 leading-relaxed max-w-2xl">{{ $faq['answer'] }}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($relatedPosts->isNotEmpty())
        <section class="max-w-[1440px] mx-auto px-4 md:px-8 pb-24 md:pb-32">
            <x-public.space-heading index="09" label="Panduan" title="Pelajari Sebelum Memutuskan" />
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($relatedPosts as $i => $post)
                    <a href="/blog/{{ $post->slug }}" data-reveal data-reveal-delay="{{ $i * 80 }}" class="group block">
                        <div class="aspect-[16/9] rounded-xl overflow-hidden bg-space-panel ring-1 ring-white/10 mb-5">
                            @if ($post->featured_image)
                                <img src="{{ \App\Support\Img::url($post->featured_image, 960) }}" srcset="{{ \App\Support\Img::srcset($post->featured_image, [480, 960]) }}" sizes="(min-width: 768px) 33vw, 100vw" alt="{{ $post->featured_image_alt ?: $post->title }}" width="640" height="360" loading="lazy" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            @endif
                        </div>
                        <h3 class="font-display font-semibold text-xl text-white tracking-tight mb-2 transition-colors group-hover:text-brand-300">{{ $post->title }}</h3>
                        <p class="text-sm text-white/55 leading-relaxed">{{ $post->excerpt }}</p>
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
