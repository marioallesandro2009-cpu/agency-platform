@php
    $service = 'Software Developer Perumahan';
    $whatsappUrl = ($settings['contact']['whatsapp_number'] ?? null)
        ? 'https://wa.me/' . $settings['contact']['whatsapp_number'] . '?text=' . rawurlencode('Halo, saya ingin diskusi sistem penjualan untuk developer perumahan kami.')
        : null;

    // Illustrative site plan: T tersedia, H ditahan, B booking, S terjual.
    $units = str_split('STBTSHTBSTTBHSTTBSTHTSBT');
    $unitStyle = [
        'T' => 'border-white/30 bg-transparent',
        'H' => 'border-amber-300 bg-amber-300',
        'B' => 'border-brand-400 bg-brand-400',
        'S' => 'border-white/30 bg-white/30',
    ];
    $legend = ['T' => 'Tersedia', 'H' => 'Ditahan', 'B' => 'Booking', 'S' => 'Terjual'];

    $pains = [
        ['title' => 'Satu unit dijanjikan ke dua pembeli', 'text' => 'Daftar stok di ponsel marketing tertinggal beberapa jam dari file admin, dan beberapa jam cukup untuk menjual kavling yang sama dua kali.', 'link' => 'aplikasi-developer-perumahan'],
        ['title' => 'Cicilan DP telat, baru ketahuan menjelang akad', 'text' => 'Tidak ada yang menagih karena semua mengira orang lain sudah melakukannya, sementara pembangunan terus memakai kas perusahaan.', 'link' => 'monitoring-cicilan-dp-rumah'],
        ['title' => 'Komisi jadi perdebatan setiap akhir bulan', 'text' => 'Skema bertahap, pembeli batal, dan kesepakatan di grup chat membuat marketing dan keuangan memegang angka yang berbeda.', 'link' => 'sistem-komisi-marketing-properti'],
    ];

    $modules = [
        ['name' => 'Denah dan status unit', 'text' => 'Satu denah yang sama untuk semua marketing, dengan status yang berubah saat itu juga.'],
        ['name' => 'Booking dan tanda jadi', 'text' => 'Penahanan unit punya batas waktu, dan tanda jadi dicocokkan oleh keuangan.'],
        ['name' => 'Cicilan DP dan penagihan', 'text' => 'Jadwal terbentuk saat booking, pengingat terkirim, dan tunggakan terlihat per proyek.'],
        ['name' => 'Komisi marketing dan agen', 'text' => 'Komisi dihitung per tahap sesuai skema proyek, dan bisa dilihat sendiri oleh penerimanya.'],
        ['name' => 'Berkas KPR dan akad', 'text' => 'Posisi berkas tiap pembeli terpantau, dan unit yang gagal bisa cepat kembali dijual.'],
        ['name' => 'Laporan direksi', 'text' => 'Unit terjual, uang masuk, dan tunggakan per proyek dalam satu halaman.'],
    ];

    $checks = [
        'Status unit masih dibagikan lewat file atau grup chat',
        'Pernah ada unit yang hampir atau benar-benar terjual ganda',
        'Tunggakan DP baru terlihat saat rekap akhir bulan',
        'Perhitungan komisi sering perlu dicek ulang',
        'Direksi harus bertanya untuk tahu posisi penjualan terkini',
    ];

    $steps = [
        ['title' => 'Diskusi gratis', 'text' => 'Anda menceritakan alur penjualan saat ini. Kalau catatan yang ada masih cukup, kami sampaikan apa adanya.'],
        ['title' => 'Pemetaan dan estimasi', 'text' => 'Alur dipetakan, lalu Anda menerima daftar modul dan estimasi tertulis.'],
        ['title' => 'Mulai dari satu modul', 'text' => 'Biasanya stok dan booking lebih dulu, lalu modul lain setelah terasa manfaatnya.'],
    ];

    $faqs = [
        ['q' => 'Berapa biayanya?', 'a' => 'Biaya ditentukan oleh jumlah modul dan proyek yang dikelola. Setelah diskusi, Anda menerima estimasi tertulis per modul, lengkap dengan asumsi dan hal yang tidak termasuk.'],
        ['q' => 'Apakah harus dibangun sekaligus?', 'a' => 'Tidak. Sistem bisa dimulai dari modul yang paling berdampak, lalu dikembangkan bertahap berdasarkan pemakaian nyata.'],
        ['q' => 'Bisa dibuka dari ponsel marketing?', 'a' => 'Bisa. Sistem dibangun sebagai aplikasi web yang bisa dibuka dari browser ponsel dan dipasang di layar utama.'],
        ['q' => 'Siapa pemilik data dan source code?', 'a' => 'Kepemilikan source code, dokumentasi, dan akses dituliskan dengan jelas di kontrak. Kami juga bersedia menandatangani NDA sebelum diskusi detail.'],
    ];

    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => 'Software Developer Perumahan',
        'serviceType' => 'Custom software development',
        'description' => 'Sistem untuk mengelola stok unit, booking, cicilan DP, komisi, dan laporan penjualan developer perumahan.',
        'url' => url('/software-developer-perumahan'),
        'areaServed' => 'ID',
        'provider' => ['@type' => 'Organization', 'name' => $settings['general']['website_name'] ?? null, 'url' => url('/')],
    ];
@endphp

<x-layouts.public
    theme="space"
    accent="blue"
    seo-title="Software Developer Perumahan: Stok, Booking, DP, Komisi"
    seo-description="Sistem untuk developer perumahan: status kavling, booking, cicilan DP, komisi marketing, dan laporan penjualan dalam satu tempat. Diskusi awal gratis."
    seo-image="/images/blog/aplikasi-developer-perumahan.jpg"
    :seo-json-ld="[$jsonLd]"
>
    <section class="corp-hero relative overflow-hidden pt-24 md:pt-32 pb-12 md:pb-16 border-b border-white/10">
        <div class="max-w-[1320px] mx-auto px-4 md:px-8 grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
            <div class="lg:col-span-7">
                <p data-reveal-load class="font-mono text-[11px] uppercase tracking-[0.14em] text-brand-300 mb-5">Untuk Developer Perumahan</p>
                <h1 data-reveal-load class="font-display font-bold text-[2.1rem] sm:text-5xl xl:text-[3.5rem] leading-[1.1] text-white mb-5 [text-wrap:balance]">Satu Sistem agar Tidak Ada Lagi Kavling yang Terjual Dua Kali</h1>
                <p data-reveal-load style="--d: 120ms" class="text-lg text-white/70 leading-relaxed max-w-2xl mb-8">Stok unit, booking, cicilan DP, dan komisi marketing tercatat di satu tempat, sehingga tim penjualan dan keuangan selalu melihat angka yang sama.</p>
                <div data-reveal-load style="--d: 200ms" class="flex flex-col sm:flex-row gap-3">
                    <a href="#diskusi" class="btn-space btn-space--lg btn-space--pill">Diskusi Gratis <span class="btn-space__arrow" aria-hidden="true">&darr;</span></a>
                    @if ($whatsappUrl)
                        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="btn-space btn-space--lg btn-space--glass">Chat WhatsApp <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
                    @endif
                </div>
            </div>

            <figure data-reveal-load style="--d: 160ms" class="lg:col-span-5 rounded-sm border border-white/15 bg-space-deep p-5 md:p-6">
                <figcaption class="flex items-center justify-between gap-3 mb-4 text-sm">
                    <span class="font-semibold text-white">Denah Cluster A</span>
                    <span class="flex items-center gap-2 font-mono text-xs text-brand-300"><span class="w-1.5 h-1.5 rounded-full bg-brand-400"></span>Status terkini</span>
                </figcaption>
                <div class="grid grid-cols-6 gap-1.5" aria-hidden="true">
                    @foreach ($units as $unit)
                        <span class="aspect-square rounded-[2px] border {{ $unitStyle[$unit] }}"></span>
                    @endforeach
                </div>
                <ul class="flex flex-wrap gap-x-5 gap-y-2 mt-4 text-xs text-white/70">
                    @foreach ($legend as $key => $label)
                        <li class="flex items-center gap-2"><span class="w-3 h-3 rounded-[2px] border {{ $unitStyle[$key] }}" aria-hidden="true"></span>{{ $label }}</li>
                    @endforeach
                </ul>
                <p class="mt-4 text-xs text-white/50">Contoh tampilan dengan data ilustrasi, bukan data klien.</p>
            </figure>
        </div>
    </section>

    <section class="max-w-[1320px] mx-auto px-4 md:px-8 py-12 md:py-16">
        <x-public.corp-heading label="Yang Sering Terjadi" title="Tiga Hal yang Diam-Diam Menggerus Penjualan" />
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            @foreach ($pains as $pain)
                <article data-reveal class="flex flex-col rounded-sm border border-white/10 border-t-2 border-t-brand-400 bg-space-deep p-5 md:p-6">
                    <h3 class="font-display font-bold text-xl leading-snug text-white mb-3">{{ $pain['title'] }}</h3>
                    <p class="text-white/65 leading-relaxed mb-4">{{ $pain['text'] }}</p>
                    <a href="/blog/{{ $pain['link'] }}" class="mt-auto text-sm font-semibold text-brand-300 underline underline-offset-4 hover:text-brand-200">Baca selengkapnya</a>
                </article>
            @endforeach
        </div>
    </section>

    <section class="border-y border-white/10 bg-space-deep/60 py-12 md:py-16">
        <div class="max-w-[1320px] mx-auto px-4 md:px-8">
            <x-public.corp-heading label="Modul" title="Pilih yang Paling Dibutuhkan Lebih Dulu" />
            <ul class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-px bg-white/15 border border-white/15">
                @foreach ($modules as $module)
                    <li data-reveal class="bg-space-void p-5 md:p-6">
                        <h3 class="font-display font-bold text-lg text-white mb-2">{{ $module['name'] }}</h3>
                        <p class="text-sm text-white/65 leading-relaxed">{{ $module['text'] }}</p>
                    </li>
                @endforeach
            </ul>
        </div>
    </section>

    <section class="force-dark corp-band py-12 md:py-16 text-white">
        <div class="max-w-[1320px] mx-auto px-4 md:px-8">
            <x-public.corp-heading label="Cek Cepat" title="Apakah Kantor Anda Sudah Membutuhkannya?" />
            <div data-need-check class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-16 items-start">
                <fieldset class="lg:col-span-7">
                    <legend class="text-white/80 mb-4">Centang yang terjadi di kantor Anda.</legend>
                    <div class="border-t border-white/25">
                        @foreach ($checks as $check)
                            <label class="flex items-start gap-4 border-b border-white/25 py-4 cursor-pointer transition-colors hover:bg-white/5">
                                <input type="checkbox" class="corp-check mt-0.5 shrink-0">
                                <span class="text-white/90 md:text-lg leading-snug">{{ $check }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
                <div class="lg:col-span-5 bg-space-void p-6 md:p-8 rounded-sm" aria-live="polite">
                    <p class="flex items-baseline justify-between font-mono text-[11px] uppercase tracking-[0.14em] text-white/60 mb-3">
                        <span>Hasil</span>
                        <span><span data-need-count class="text-white">0</span> dari {{ count($checks) }}</span>
                    </p>
                    <div class="h-1 bg-white/15 mb-6" aria-hidden="true"><div data-need-bar class="h-full bg-brand-400 transition-all duration-300" style="width: 0%"></div></div>
                    <div data-need-verdict="0">
                        <p class="font-display font-bold text-2xl text-white mb-2">Catatan Anda masih memadai</p>
                        <p class="text-white/70 leading-relaxed">Bila hanya sedikit yang terasa, cara kerja sekarang mungkin masih cukup.</p>
                    </div>
                    <div data-need-verdict="1" class="hidden">
                        <p class="font-display font-bold text-2xl text-white mb-2">Layak dipetakan</p>
                        <p class="text-white/70 leading-relaxed">Hambatannya mulai berulang, dan biasanya bertambah seiring jumlah unit dan marketing.</p>
                    </div>
                    <div data-need-verdict="2" class="hidden">
                        <p class="font-display font-bold text-2xl text-white mb-2">Penjualan Anda sudah terbebani</p>
                        <p class="text-white/70 leading-relaxed">Sebaiknya alurnya segera dipetakan sebelum proyek berikutnya dimulai.</p>
                    </div>
                    <a href="#diskusi" class="btn-space btn-space--pill mt-6">Bahas Hasil Ini <span class="btn-space__arrow" aria-hidden="true">&darr;</span></a>
                </div>
            </div>
        </div>
    </section>

    <section class="max-w-[1320px] mx-auto px-4 md:px-8 py-12 md:py-16">
        <x-public.corp-heading label="Cara Mulai" title="Tiga Langkah, Tanpa Harus Membangun Semuanya" />
        <ol class="grid grid-cols-1 md:grid-cols-3 gap-px bg-white/15 border border-white/15">
            @foreach ($steps as $i => $step)
                <li data-reveal class="bg-space-void p-5 md:p-6">
                    <span class="block font-mono text-sm text-brand-300 mb-4">0{{ $i + 1 }}</span>
                    <h3 class="font-display font-bold text-lg text-white mb-2">{{ $step['title'] }}</h3>
                    <p class="text-sm text-white/65 leading-relaxed">{{ $step['text'] }}</p>
                </li>
            @endforeach
        </ol>
    </section>

    <section class="max-w-[1320px] mx-auto px-4 md:px-8 pb-12 md:pb-16">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-16">
            <div class="lg:col-span-4">
                <x-public.corp-heading label="FAQ" title="Pertanyaan Umum" class="!mb-0" />
            </div>
            <div class="lg:col-span-8 border-t border-white/15">
                @foreach ($faqs as $faq)
                    <details class="group border-b border-white/15">
                        <summary class="flex items-start justify-between gap-6 py-5 cursor-pointer list-none font-display font-bold text-lg text-white [&::-webkit-details-marker]:hidden">
                            {{ $faq['q'] }}
                            <span class="text-brand-300 text-xl leading-none transition-transform duration-200 group-open:rotate-45" aria-hidden="true">+</span>
                        </summary>
                        <p class="pb-6 text-white/65 leading-relaxed max-w-2xl">{{ $faq['a'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    <div id="diskusi" class="scroll-mt-24">
        <x-public.contact-cta
            form-id="developer-cta-form"
            eyebrow="Diskusi Gratis"
            title="Ceritakan Cara Tim Anda Mencatat Unit Hari Ini"
            description="Tanpa biaya dan tanpa kewajiban. Anda akan mendapat gambaran modul yang paling berguna, meski akhirnya memutuskan belum membuat sistem."
            source="konsultasi"
            :service-interested="$service"
        />
    </div>
</x-layouts.public>
