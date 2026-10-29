@php
    $categories = [
        ['value' => 'landing_page', 'label' => 'Landing Page'],
        ['value' => 'company_profile', 'label' => 'Company Profile'],
        ['value' => 'ecommerce', 'label' => 'E-commerce'],
        ['value' => 'sistem_informasi', 'label' => 'Sistem Informasi'],
    ];
    $tierLabels = ['seed' => 'Seed', 'growth' => 'Growth', 'scale' => 'Scale'];
    $tierOrder = ['seed' => 0, 'growth' => 1, 'scale' => 2];

    $grouped = [];
    foreach ($categories as $cat) {
        $grouped[$cat['value']] = $packages->where('category', $cat['value'])
            ->sortBy(fn ($pkg) => $tierOrder[$pkg->tier] ?? 99)
            ->values();
    }

    $initialCategory = request('kategori');
    if (!$initialCategory || !isset($grouped[$initialCategory]) || $grouped[$initialCategory]->isEmpty()) {
        $initialCategory = collect($categories)->first(fn ($c) => $grouped[$c['value']]->isNotEmpty())['value'] ?? $categories[0]['value'];
    }

    // Space theme: Growth is the lit-up "featured" tier, the others are glass.
    $tierStyles = [
        'seed' => ['card' => 'border-white/10 bg-white/[0.03]', 'price' => 'text-white', 'cta' => 'btn-space--glass'],
        'growth' => ['card' => 'border-brand-400/60 bg-space-deep shadow-[0_0_80px_-20px_rgb(var(--lime-400)/0.45)]', 'price' => 'text-brand-300', 'cta' => 'btn-space--pill'],
        'scale' => ['card' => 'border-white/10 bg-white/[0.03]', 'price' => 'text-white', 'cta' => 'btn-space--glass'],
    ];

    $includedInAll = ['Email Bisnis Gratis (@namadomainanda.com)', 'Desain Responsive', 'Fondasi SEO Friendly', 'Desain Custom (bukan template)'];

    $websiteName = $settings['general']['website_name'] ?? 'Kami';
@endphp

<x-layouts.public
    theme="space"
    seo-title="Harga Paket Website"
    seo-description="Daftar harga paket pembuatan website untuk landing page, company profile, e-commerce, dan sistem informasi."
>
    @if (!empty($recentActivity))
        <div
            data-live-ticker='{{ json_encode($recentActivity, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) }}'
            role="status"
            class="hidden fixed left-4 bottom-5 z-40 max-w-[calc(100vw-7rem)] sm:max-w-sm flex items-center gap-3 rounded-full border border-white/10 bg-space-deep/80 backdrop-blur-xl pl-3 pr-2 py-2 font-mono text-[11px] text-white/70 shadow-2xl"
        >
            <span class="relative flex h-2 w-2 shrink-0" aria-hidden="true">
                <span class="absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75 animate-ping motion-reduce:animate-none"></span>
                <span class="relative inline-flex h-2 w-2 rounded-full bg-brand-400"></span>
            </span>
            <p data-live-ticker-text class="min-w-0 truncate [&_span]:text-white [&_span]:font-normal"></p>
            <button data-live-ticker-close aria-label="Tutup notifikasi" class="shrink-0 w-6 h-6 rounded-full text-white/50 hover:text-white hover:bg-white/10 leading-none">&times;</button>
        </div>
    @endif

    <x-public.space-page-header
        eyebrow="Harga"
        code="$$"
        :title="'Harga Paket Jasa Pembuatan Website ' . $websiteName"
        subtitle="Pilih kategori dan tier sesuai kebutuhan. Harga akhir dapat menyesuaikan kebutuhan, jumlah halaman, fitur, integrasi, dan kompleksitas project."
    />

    <section class="py-16 md:py-24">
        <div class="max-w-[1440px] mx-auto px-4 md:px-8">
            <div class="flex flex-wrap items-center gap-2 mb-14" role="tablist" aria-label="Kategori paket">
                <span class="hidden sm:inline font-mono text-[10px] uppercase tracking-[0.25em] text-white/40 mr-3">Kategori &rarr;</span>
                @foreach ($categories as $cat)
                    <button
                        type="button"
                        data-tab-trigger="{{ $cat['value'] }}"
                        data-aria-styled
                        role="tab"
                        aria-selected="{{ $cat['value'] === $initialCategory ? 'true' : 'false' }}"
                        class="space-tab px-5 py-2.5 rounded-full font-mono text-[11px] uppercase tracking-[0.15em]"
                    >
                        {{ $cat['label'] }}
                    </button>
                @endforeach
            </div>

            @foreach ($categories as $cat)
                @php $catPackages = $grouped[$cat['value']]; @endphp
                <div data-tab-panel="{{ $cat['value'] }}" class="{{ $cat['value'] === $initialCategory ? '' : 'hidden' }}">
                    @if ($catPackages->isNotEmpty())
                        {{-- Tiers as a staggered constellation, not three equal boxes: Growth
                             is wider and sits highest, Seed and Scale hang at
                             different depths beside it. --}}
                        @php $tierOffset = ['seed' => 'lg:mt-20', 'growth' => '', 'scale' => 'lg:mt-36']; @endphp
                        <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.3fr_1fr] gap-5 lg:gap-6 items-start pt-4">
                            @foreach ($catPackages as $index => $pkg)
                                @php $style = $tierStyles[$pkg->tier] ?? $tierStyles['seed']; @endphp
                                <div class="group relative overflow-hidden flex flex-col rounded-2xl border backdrop-blur-md p-7 {{ $pkg->tier === 'growth' ? 'md:p-10' : 'md:p-8' }} transition-transform duration-500 hover:-translate-y-1 {{ $style['card'] }} {{ $tierOffset[$pkg->tier] ?? '' }}">
                                    <span class="absolute -right-3 -top-6 font-display font-extrabold text-[8rem] leading-none text-transparent [-webkit-text-stroke:1px_rgb(var(--ink)/0.07)] pointer-events-none select-none" aria-hidden="true">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    <div class="flex items-center justify-between mb-8">
                                        <span class="font-mono text-[10px] uppercase tracking-[0.25em] {{ $pkg->tier === 'growth' ? 'text-brand-400' : 'text-white/45' }}">
                                            /{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }} &mdash; {{ $tierLabels[$pkg->tier] ?? $pkg->name }}
                                        </span>
                                        @if ($pkg->tier === 'growth')
                                            <span class="font-mono text-[10px] uppercase tracking-[0.18em] bg-brand-400 text-space-void px-3 py-1 rounded-full">
                                                {{ $pkg->badge ?: 'Best Value' }}
                                            </span>
                                        @endif
                                    </div>

                                    <h2 class="font-display font-semibold text-2xl text-white tracking-tight">{{ $tierLabels[$pkg->tier] ?? $pkg->name }}</h2>
                                    @if ($pkg->tagline)
                                        <p class="mt-1 text-sm text-white/50">{{ $pkg->tagline }}</p>
                                    @endif

                                    <div class="mt-8 pb-8 border-b border-white/10">
                                        @if ($pkg->price_prefix)
                                            <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-white/40 mb-1">{{ $pkg->price_prefix }}</p>
                                        @endif
                                        <p class="font-display {{ $pkg->tier === 'growth' ? 'text-4xl md:text-5xl' : 'text-3xl md:text-4xl' }} leading-tight tabular-nums {{ $style['price'] }}">
                                            {{ $pkg->price ? \App\Support\PriceFormatter::idr($pkg->price) : $pkg->price_suffix }}
                                        </p>
                                        @if ($pkg->price && $pkg->price_suffix)
                                            <p class="mt-1 text-xs text-white/45">{{ $pkg->price_suffix }}</p>
                                        @endif
                                        @if ($pkg->value_note)
                                            <p class="mt-3 text-xs leading-snug text-white/50">{{ $pkg->value_note }}</p>
                                        @endif
                                        @if ($pkg->estimated_days)
                                            <p class="mt-3 inline-flex items-center gap-2 font-mono text-[10px] uppercase tracking-[0.18em] text-white/55 border border-white/10 rounded-full px-3 py-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-brand-400"></span>
                                                Selesai dalam {{ $pkg->estimated_days }}
                                            </p>
                                        @endif
                                    </div>

                                    <ul class="space-y-3.5 flex-1 my-8">
                                        @foreach (($pkg->features ?? []) as $feature)
                                            <li class="flex items-start gap-3 text-sm text-white/75">
                                                <span class="w-3 h-px bg-brand-400 shrink-0 mt-[0.7rem]" aria-hidden="true"></span>
                                                <span>
                                                    {{ $feature['label'] }}
                                                    @if (!empty($feature['note']))
                                                        <span class="block text-xs text-white/40 mt-0.5">{{ $feature['note'] }}</span>
                                                    @endif
                                                </span>
                                            </li>
                                        @endforeach
                                    </ul>

                                    <a href="/konsultasi?kategori={{ $pkg->category }}&tier={{ $pkg->tier }}" class="btn-space btn-space--lg {{ $style['cta'] }} justify-between w-full">
                                        {{ $pkg->cta_text ?: 'Pesan Website' }} <span class="btn-space__arrow" aria-hidden="true">&rarr;</span>
                                    </a>
                                </div>
                            @endforeach
                        </div>

                        {{-- Included in every package: read as one flowing manifesto
                             line with star separators instead of a 4-cell grid. --}}
                        <div class="mt-20 grid grid-cols-1 lg:grid-cols-12 gap-6 items-baseline">
                            <h3 class="lg:col-span-3 font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400">Sudah termasuk<br class="hidden lg:block"> di semua paket</h3>
                            <p class="lg:col-span-9 font-display text-xl md:text-3xl leading-[1.35] tracking-tight text-white/85">
                                @foreach ($includedInAll as $i => $item)
                                    <span class="lg:whitespace-nowrap">{{ $item }}</span>@if (!$loop->last)<span class="text-brand-400 mx-3 md:mx-4 align-middle text-base md:text-xl" aria-hidden="true">&#10022;</span>@endif
                                @endforeach
                            </p>
                        </div>

                        {{-- Terms: three statements stepping down and across the page. --}}
                        @php
                            $terms = [
                                ['DP 50%', 'Sisanya setelah website siap tayang', ''],
                                ['Garansi bug 30 hari', 'Setelah serah terima', 'lg:ml-[22%]'],
                                ['Konsultasi 30 menit gratis', 'Tanpa komitmen', 'lg:ml-[44%]'],
                            ];
                        @endphp
                        <ul class="mt-16 space-y-6 border-t border-white/10 pt-10">
                            @foreach ($terms as $i => [$termTitle, $termNote, $termIndent])
                                <li data-reveal data-reveal-delay="{{ $i * 100 }}" class="flex items-baseline gap-5 {{ $termIndent }}">
                                    <span class="font-mono text-[10px] tracking-[0.2em] text-brand-400">T{{ $i + 1 }}</span>
                                    <span>
                                        <span class="block font-display font-semibold text-2xl md:text-3xl tracking-tight text-white">{{ $termTitle }}</span>
                                        <span class="block font-mono text-[10px] uppercase tracking-[0.2em] text-white/45 mt-1.5">{{ $termNote }}</span>
                                    </span>
                                </li>
                            @endforeach
                        </ul>

                        @php
                            $comparisonRows = [];
                            foreach ($catPackages as $pkg) {
                                foreach (($pkg->comparison_features ?? []) as $row) {
                                    if (!empty($row['feature']) && !in_array($row['feature'], $comparisonRows, true)) {
                                        $comparisonRows[] = $row['feature'];
                                    }
                                }
                            }
                        @endphp
                        @if (!empty($comparisonRows))
                            <div class="mt-16">
                                <x-public.space-heading index="01" label="Perbandingan" title="Perbandingan Fitur per Tier" />
                                <div class="rounded-2xl border border-white/10 overflow-hidden">
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm min-w-[520px]">
                                            <thead>
                                                <tr class="border-b border-white/10 bg-white/[0.03]">
                                                    <th class="text-left font-mono text-[10px] uppercase tracking-[0.2em] font-normal text-white/45 px-6 py-4 w-2/5">Fitur</th>
                                                    @foreach ($catPackages as $pkg)
                                                        <th class="text-center font-display font-semibold px-4 py-4 {{ $pkg->tier === 'growth' ? 'text-brand-300 bg-brand-400/[0.06]' : 'text-white' }}">
                                                            {{ $tierLabels[$pkg->tier] ?? $pkg->tier }}
                                                        </th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($comparisonRows as $label)
                                                    <tr class="border-b border-white/5 last:border-b-0 hover:bg-white/[0.02]">
                                                        <td class="px-6 py-3.5 text-white/70">{{ $label }}</td>
                                                        @foreach ($catPackages as $pkg)
                                                            @php
                                                                $value = collect($pkg->comparison_features ?? [])->firstWhere('feature', $label)['value'] ?? null;
                                                            @endphp
                                                            <td class="text-center px-4 py-3.5 {{ $pkg->tier === 'growth' ? 'bg-brand-400/[0.06] text-white' : 'text-white/60' }}">
                                                                @if ($value && trim($value) !== '-')
                                                                    {{ $value }}
                                                                @else
                                                                    <span class="inline-block w-3 h-px bg-white/20 align-middle" aria-label="Tidak tersedia"></span>
                                                                @endif
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Renewal --}}
                        <div class="mt-16 rounded-2xl border border-white/10 bg-white/[0.02] p-6 md:p-8">
                            <h3 class="font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400 mb-6">Harga renewal per tier</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
                                @foreach ($catPackages as $pkg)
                                    <div class="flex items-center justify-between rounded-xl border border-white/10 px-5 py-4">
                                        <span class="font-mono text-[11px] uppercase tracking-[0.18em] text-white/55">{{ $tierLabels[$pkg->tier] ?? $pkg->tier }}</span>
                                        <span class="font-display text-white tabular-nums">
                                            {{ $pkg->renewal_price ? \App\Support\PriceFormatter::idr($pkg->renewal_price) . '/tahun' : '-' }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                            <p class="text-xs text-white/45">
                                Sudah termasuk domain + hosting tahun pertama pada harga paket di atas. Renewal berlaku mulai tahun kedua.
                            </p>
                        </div>
                    @else
                        <p class="text-center text-white/50 py-16 font-mono text-xs uppercase tracking-[0.2em]">Paket untuk kategori ini akan segera hadir.</p>
                    @endif
                </div>
            @endforeach
        </div>
    </section>

    @if ($maintenancePlans->isNotEmpty())
        <section class="py-16 md:py-24 border-t border-white/10">
            <div class="max-w-[1440px] mx-auto px-4 md:px-8">
                <x-public.space-heading index="02" label="Maintenance" title="Paket Maintenance Lanjutan">
                    <p class="mt-5 text-white/55 max-w-2xl leading-relaxed">
                        Setelah masa maintenance gratis habis, pilih paket lanjutan sesuai kebutuhan Anda — atau tetap tanpa
                        paket, dengan garansi bug-fixing dasar tetap berlaku.
                    </p>
                </x-public.space-heading>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    @foreach ($maintenancePlans as $plan)
                        @php
                            $waNumber = $settings['contact']['whatsapp_number'] ?? null;
                            $waLink = $waNumber
                                ? 'https://wa.me/' . $waNumber . '?text=' . rawurlencode("Halo, saya ingin tanya soal paket maintenance {$plan->name}.")
                                : '/konsultasi';
                        @endphp
                        <div class="rounded-2xl border border-white/10 bg-white/[0.03] p-7 flex flex-col">
                            <h3 class="font-display font-semibold text-xl text-white tracking-tight">{{ $plan->name }}</h3>

                            @if ($plan->price_yearly)
                                <div class="inline-flex items-center gap-1 mt-4 mb-5 self-start">
                                    <button type="button" data-billing-trigger="{{ $plan->id }}:monthly" data-aria-styled aria-pressed="true" class="space-tab font-mono text-[10px] uppercase tracking-[0.15em] px-3 py-1.5 rounded-full">Bulanan</button>
                                    <button type="button" data-billing-trigger="{{ $plan->id }}:yearly" data-aria-styled aria-pressed="false" class="space-tab font-mono text-[10px] uppercase tracking-[0.15em] px-3 py-1.5 rounded-full">Tahunan</button>
                                </div>
                            @endif

                            <div class="mb-6 {{ $plan->price_yearly ? '' : 'mt-4' }}">
                                <div data-billing-panel="{{ $plan->id }}:monthly" class="flex items-baseline gap-2 flex-wrap">
                                    <p class="font-display text-2xl text-brand-300 tabular-nums">{{ \App\Support\PriceFormatter::idr($plan->price) }}<span class="text-sm text-white/45">/bulan</span></p>
                                    @if ($plan->original_price_monthly && $plan->original_price_monthly > $plan->price)
                                        <span class="text-sm text-white/35 line-through">{{ \App\Support\PriceFormatter::idr($plan->original_price_monthly) }}</span>
                                    @endif
                                </div>
                                @if ($plan->original_price_monthly && $plan->original_price_monthly > $plan->price)
                                    @php $percent = round((1 - $plan->price / $plan->original_price_monthly) * 100); @endphp
                                    <span data-billing-panel="{{ $plan->id }}:monthly" class="inline-block mt-2 font-mono text-[10px] text-brand-300 border border-brand-400/30 bg-brand-400/10 px-2 py-0.5 rounded-full">-{{ $percent }}%</span>
                                @endif

                                @if ($plan->price_yearly)
                                    @php
                                        $listPrice = $plan->price * 12;
                                        $yearlyOriginal = null;
                                        $yearlyBadge = null;
                                        if ($plan->original_price_yearly && $plan->original_price_yearly > $plan->price_yearly) {
                                            $yearlyOriginal = $plan->original_price_yearly;
                                            $yearlyPercent = round((1 - $plan->price_yearly / $yearlyOriginal) * 100);
                                            $yearlyBadge = "-{$yearlyPercent}%";
                                        } elseif ($listPrice > $plan->price_yearly) {
                                            $yearlyOriginal = $listPrice;
                                            $yearlyPercent = round((1 - $plan->price_yearly / $listPrice) * 100);
                                            $months = round(($listPrice - $plan->price_yearly) / $plan->price);
                                            $yearlyBadge = "-{$yearlyPercent}% · Hemat {$months} bulan";
                                        }
                                    @endphp
                                    <div data-billing-panel="{{ $plan->id }}:yearly" class="hidden flex items-baseline gap-2 flex-wrap">
                                        <p class="font-display text-2xl text-brand-300 tabular-nums">{{ \App\Support\PriceFormatter::idr($plan->price_yearly) }}<span class="text-sm text-white/45">/tahun</span></p>
                                        @if ($yearlyOriginal)
                                            <span class="text-sm text-white/35 line-through">{{ \App\Support\PriceFormatter::idr($yearlyOriginal) }}</span>
                                        @endif
                                    </div>
                                    @if ($yearlyBadge)
                                        <span data-billing-panel="{{ $plan->id }}:yearly" class="hidden mt-2 font-mono text-[10px] text-brand-300 border border-brand-400/30 bg-brand-400/10 px-2 py-0.5 rounded-full">{{ $yearlyBadge }}</span>
                                    @endif
                                @endif
                            </div>

                            <ul class="space-y-3 flex-1 mb-8 pt-6 border-t border-white/10">
                                @foreach (($plan->features ?? []) as $feature)
                                    <li class="flex items-start gap-3 text-sm text-white/70">
                                        <span class="w-3 h-px bg-brand-400 shrink-0 mt-[0.7rem]" aria-hidden="true"></span>
                                        <span>{{ $feature }}</span>
                                    </li>
                                @endforeach
                            </ul>

                            <a href="{{ $waLink }}" target="_blank" rel="noopener" class="btn-space btn-space--glass justify-between w-full">
                                {{ $plan->cta_text ?: 'Tanya Lebih Lanjut' }} <span class="btn-space__arrow" aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    @endforeach
                </div>

                <p class="text-xs text-white/40 max-w-2xl mt-8">
                    Paket maintenance opsional. Website Anda tetap mendapat garansi bug-fixing sesuai ketentuan tier yang
                    dipilih, meski tanpa paket maintenance lanjutan.
                </p>
            </div>
        </section>
    @endif

    <div class="max-w-[1440px] mx-auto px-4 md:px-8 pb-20">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">
            <div class="lg:col-span-5 rounded-2xl border border-white/10 bg-white/[0.03] p-6 md:p-8 flex flex-col justify-between gap-6">
                <p class="font-display text-xl text-white leading-snug">Butuh scope di luar paket ini? Kami terbuka untuk project custom.</p>
                <a href="/konsultasi" class="btn-space btn-space--pill self-start">Konsultasikan kebutuhan <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
            </div>
            <div class="lg:col-span-7 rounded-2xl border border-white/10 p-6 md:p-8">
                <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-white/40 mb-4">Catatan biaya</p>
                <p class="text-sm text-white/55 leading-relaxed">
                    Biaya transaksi payment gateway, kuota API pihak ketiga (ongkir, notifikasi WhatsApp), dan
                    langganan layanan eksternal ditanggung terpisah oleh klien sesuai pemakaian aktual. Setup dan
                    pengelolaan domain &amp; hosting kami tangani penuh — Anda tidak perlu membuat akun atau mengelola
                    panel sendiri, dan domain tetap terdaftar atas nama Anda (WHOIS Anda). Domain &amp; hosting dapat
                    diperpanjang tahunan sesuai harga berlaku saat itu.
                </p>
            </div>
        </div>
    </div>
</x-layouts.public>
