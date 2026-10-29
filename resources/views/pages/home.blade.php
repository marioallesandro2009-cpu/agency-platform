@php
    $organizationJsonLd = []; // Organization JSON-LD already rendered site-wide by the layout's seo-meta partial.

    $cheapestByCategory = [];
    $priceCategories = [
        ['value' => 'landing_page', 'label' => 'Landing Page'],
        ['value' => 'company_profile', 'label' => 'Company Profile'],
        ['value' => 'ecommerce', 'label' => 'E-commerce'],
        ['value' => 'sistem_informasi', 'label' => 'Sistem Informasi'],
    ];
    foreach ($priceCategories as $cat) {
        $cheapestByCategory[$cat['value']] = $pricingPackages
            ->where('category', $cat['value'])
            ->pluck('price')
            ->filter()
            ->min();
    }

    // HUD section numbers ("[ 03 ]") follow the admin-configured order.
    $sectionNumber = fn ($type) => collect($sections)->search($type);

    // Headline treatment from the reference: lighter lead-in words,
    // heavier closing words ("INSIDE THE / TESSERACT"). Admin text, so
    // split by word count rather than by meaning.
    $heroWords = preg_split('/\s+/', trim($hero['title'] ?? ''), -1, PREG_SPLIT_NO_EMPTY);
    $heroTailCount = count($heroWords) >= 3 ? (int) ceil(count($heroWords) / 3) : count($heroWords);
    $heroLead = implode(' ', array_slice($heroWords, 0, count($heroWords) - $heroTailCount));
    $heroTail = implode(' ', array_slice($heroWords, -$heroTailCount ?: 0));

    // Lucide icon paths (the Blade pages don't load lucide-vue-next).
    $processIcons = [
        '<path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>',
        '<path d="m12 19 7-7 3 3-7 7-3-3z"/><path d="m18 13-1.5-7.5L2 2l3.5 14.5L13 18l5-5z"/><path d="m2 2 7.586 7.586"/><circle cx="11" cy="11" r="2"/>',
        '<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>',
        '<path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/>',
    ];
    $categoryIcons = [
        'landing_page' => $processIcons[3],
        'company_profile' => '<path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h2"/><path d="M18 9h2a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-2"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/>',
        'ecommerce' => '<circle cx="8" cy="21" r="1"/><circle cx="19" cy="21" r="1"/><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"/>',
        'sistem_informasi' => '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M3 5V19A9 3 0 0 0 21 19V5"/><path d="M3 12A9 3 0 0 0 21 12"/>',
    ];
    $sparkIcon = '<path d="M12 3l1.9 5.8L20 10.7l-6.1 1.9L12 18.4l-1.9-5.8L4 10.7l6.1-1.9L12 3z"/>';
@endphp

<x-layouts.public theme="space" :seo-title="$settings['general']['tagline'] ?? null" :seo-json-ld="$organizationJsonLd">

    @if (!empty($recentActivity))
        {{-- Floating HUD pill (bottom-left) instead of a full-width bar, so it doesn't sit on top of the full-screen hero. --}}
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

    @foreach ($sections as $type)

        {{-- Hero: full-screen dot-matrix planet --}}
        @if ($type === 'hero' && $hero)
            <section class="space-hero force-dark relative min-h-[100svh] flex flex-col overflow-hidden bg-space-void text-white">
                <div class="absolute inset-0 z-0" data-vue-island="Planet"></div>
                <div class="space-hero__scrim absolute inset-0 z-[1] pointer-events-none" aria-hidden="true"></div>

                {{-- HUD: object label beside the planet --}}
                <div class="hidden md:block absolute z-[2] top-[15%] right-[5%] font-mono text-[10px] uppercase tracking-[0.22em] text-white/50" aria-hidden="true">
                    <p class="text-brand-400 mb-1">[ exo ] &mdash; 01</p>
                    <p class="text-white/80">Planet {{ $settings['general']['website_name'] ?? '' }}</p>
                    <p>Orbit stabil &middot; 24/7 online</p>
                </div>
                {{-- HUD: vertical side label --}}
                <p class="hidden lg:block absolute z-[2] right-6 top-1/2 -translate-y-1/2 [writing-mode:vertical-rl] font-mono text-[10px] uppercase tracking-[0.3em] text-white/30" aria-hidden="true">
                    Est. web &middot; design &middot; engineering
                </p>

                <div class="relative z-10 flex-1 flex items-end max-w-[1440px] w-full mx-auto px-4 md:px-8 pt-32 pb-28 md:pb-32">
                    <div class="max-w-4xl">
                        <p data-reveal class="flex items-center gap-3 font-mono text-[10px] md:text-[11px] uppercase tracking-[0.25em] text-white/55 mb-6">
                            <span class="text-brand-400">[ 00 ]</span>
                            <span class="h-px w-10 bg-white/25"></span>
                            {{ $hero['eyebrow'] ?? '' }}
                        </p>
                        <h1 data-reveal data-reveal-delay="80" class="font-display uppercase leading-[0.95] tracking-tight mb-7 [text-wrap:balance]">
                            @if ($heroLead)
                                <span class="block font-light text-[2rem] sm:text-5xl lg:text-6xl xl:text-7xl 2xl:text-[5.25rem] [overflow-wrap:anywhere] text-white/90">{{ $heroLead }}</span>
                            @endif
                            <span class="block font-extrabold text-[2rem] sm:text-5xl lg:text-6xl xl:text-7xl 2xl:text-[5.25rem] [overflow-wrap:anywhere] space-hero__title-glow">{{ $heroTail }}</span>
                        </h1>
                        <p data-reveal data-reveal-delay="160" class="text-base md:text-lg text-white/60 leading-relaxed max-w-xl mb-10">
                            {{ $hero['subtitle'] ?? '' }}
                        </p>
                        <div data-reveal data-reveal-delay="240" class="flex flex-wrap gap-3">
                            <a href="{{ $hero['cta_primary_url'] ?? '/konsultasi' }}" class="btn-space btn-space--lg btn-space--pill">
                                {{ $hero['cta_primary_text'] ?? 'Konsultasi Gratis' }} <span class="btn-space__arrow" aria-hidden="true">&rarr;</span>
                            </a>
                            <a href="{{ $hero['cta_secondary_url'] ?? '/portfolio' }}" class="btn-space btn-space--lg btn-space--glass">
                                {{ $hero['cta_secondary_text'] ?? 'Lihat Portfolio' }} <span class="btn-space__arrow" aria-hidden="true">&rarr;</span>
                            </a>
                        </div>
                    </div>
                </div>

                {{-- HUD bottom bar --}}
                <div class="absolute z-10 inset-x-0 bottom-0 border-t border-white/10" aria-hidden="true">
                    <div class="max-w-[1440px] mx-auto px-4 md:px-8 h-14 flex items-center justify-between gap-6 font-mono text-[10px] uppercase tracking-[0.22em] text-white/40">
                        <span class="flex items-center gap-3">
                            <span class="scroll-cue relative block w-px h-6 bg-white/15 overflow-hidden"><span class="absolute inset-x-0 top-0 h-2 bg-brand-400"></span></span>
                            Scroll untuk menjelajah
                        </span>
                        <span class="hidden md:inline">06&deg;12&prime; S &mdash; 106&deg;49&prime; E</span>
                        <span class="hidden sm:flex items-center gap-2 tabular-nums">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-400 animate-pulse motion-reduce:animate-none"></span>
                            <span data-hud-clock>--:--:-- WIB</span>
                        </span>
                    </div>
                </div>
            </section>
        @endif

        {{-- About --}}
        @if ($type === 'about' && $about)
            <section class="py-24 md:py-36 overflow-hidden">
                <div class="max-w-[1440px] mx-auto px-4 md:px-8">
                    <div class="grid grid-cols-1 lg:grid-cols-12 lg:items-center gap-8 lg:gap-0">
                        <div data-reveal="right" class="lg:col-start-6 lg:col-span-7 lg:row-start-1 relative z-10 rounded-2xl border border-white/10 bg-space-deep/70 backdrop-blur-xl p-8 md:p-12 mx-4 lg:mx-0 shadow-[0_40px_80px_-30px_rgba(0,0,0,0.8)]">
                            <div class="flex items-center gap-4 font-mono text-[10px] uppercase tracking-[0.25em] text-white/45 mb-6">
                                <span class="text-brand-400">[ {{ str_pad($sectionNumber('about'), 2, '0', STR_PAD_LEFT) }} ]</span>
                                <span>Tentang kami</span>
                                <span class="h-px flex-1 bg-gradient-to-r from-white/20 to-transparent"></span>
                            </div>
                            <h2 class="font-display font-semibold text-3xl md:text-5xl tracking-tight text-white mb-6 leading-[1.05] text-left [text-wrap:balance]">
                                {{ $about['title'] ?? '' }}
                            </h2>
                            <p class="text-white/60 text-left leading-relaxed mb-8">{{ $about['description'] ?? '' }}</p>
                            @if (!empty($about['button_text']))
                                <a href="{{ $about['button_url'] ?: '/tentang-kami' }}" class="btn-space btn-space--lg btn-space--pill">
                                    {{ $about['button_text'] }} <span class="btn-space__arrow" aria-hidden="true">&rarr;</span>
                                </a>
                            @endif
                            @if (!empty($about['extra_note']))
                                <p class="font-mono text-xs text-white/40 mt-5">{{ $about['extra_note'] }}</p>
                            @endif

                            @if ($mascots['kenalan_dekat_kami']['image_path'] ?? null)
                                <img src="{{ $mascots['kenalan_dekat_kami']['image_path'] }}" alt="{{ $mascots['kenalan_dekat_kami']['alt_text'] ?? '' }}" width="220" height="220" loading="lazy" class="w-full max-w-[180px] mt-8 object-contain">
                            @endif
                        </div>

                        <div class="lg:col-start-1 lg:col-span-7 lg:row-start-1">
                            <div class="rounded-2xl overflow-hidden aspect-[4/3] lg:aspect-[3/4] bg-space-panel ring-1 ring-white/10">
                                @if (!empty($about['image']))
                                    <img data-reveal-image src="{{ $about['image'] }}" alt="{{ $about['title'] ?? '' }}" width="700" height="875" loading="lazy" class="w-full h-full object-cover">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- Services --}}
        @if ($type === 'services')
            <section class="relative max-w-[1440px] mx-auto px-4 md:px-8 py-24 md:py-32">
                <x-public.space-heading
                    :index="$sectionNumber('services')"
                    :label="$servicesSection['subtitle'] ?? 'Layanan Kami'"
                    :title="$servicesSection['title'] ?? 'Solusi Website untuk Setiap Kebutuhan'"
                />
                @foreach ($services as $index => $service)
                    <div data-reveal data-reveal-delay="{{ min($index * 60, 240) }}" class="py-4 md:py-5">
                        <div data-glow class="group relative overflow-hidden rounded-2xl border border-white/10 bg-space-deep/60 backdrop-blur-md p-6 md:p-10 transition-colors duration-300 hover:border-brand-400/40">
                            <div class="space-card__glow pointer-events-none absolute inset-0 opacity-0 transition-opacity duration-300 group-hover:opacity-100" aria-hidden="true"></div>

                            <div class="relative grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                                <div class="lg:col-span-6 order-2 {{ $index % 2 === 1 ? 'lg:order-2' : 'lg:order-1' }}">
                                    <div class="flex items-start justify-between gap-4 mb-6">
                                        <span class="font-display font-extrabold text-6xl md:text-7xl leading-none tabular-nums text-transparent [-webkit-text-stroke:1px_rgb(var(--ink)/0.22)] transition-all duration-500 group-hover:[-webkit-text-stroke:1px_rgb(var(--lime-400)/0.8)]" aria-hidden="true">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                        @if ($service->badge_label)
                                            <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-300 border border-brand-400/30 bg-brand-400/10 px-2.5 py-1 rounded-full">{{ $service->badge_label }}</p>
                                        @endif
                                    </div>
                                    <h3 class="font-display font-semibold text-2xl md:text-3xl text-white tracking-tight mb-4">{{ $service->name }}</h3>
                                    <p class="text-white/55 leading-relaxed mb-5 max-w-md">{{ $service->short_description }}</p>
                                    <ul class="text-sm text-white/70 space-y-2 mb-7">
                                        @foreach (collect($service->features ?? [])->take(3) as $feature)
                                            <li class="flex items-start gap-3">
                                                <span class="w-3 h-px bg-brand-400 shrink-0 mt-[0.7rem]" aria-hidden="true"></span>
                                                <span>{{ $feature }}</span>
                                            </li>
                                        @endforeach
                                    </ul>
                                    <a href="/{{ $service->slug }}" class="inline-flex items-center gap-2 text-brand-300 hover:text-brand-200 font-mono text-xs uppercase tracking-[0.18em] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-400 rounded">
                                        Pelajari lebih lanjut
                                        <span aria-hidden="true" class="transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
                                    </a>
                                </div>
                                <div class="lg:col-span-6 order-1 {{ $index % 2 === 1 ? 'lg:order-1' : 'lg:order-2' }}">
                                    <div class="hud-frame relative p-2">
                                        <div class="aspect-[16/10] rounded-lg overflow-hidden bg-space-panel">
                                            <img data-reveal-image src="{{ $service->image ?: 'https://placehold.co/640x400/0b0d14/0b0d14?text=%20' }}" alt="{{ $service->image ? $service->name : '' }}" width="640" height="400" loading="lazy" class="w-full h-full object-cover opacity-90 transition-all duration-700 group-hover:scale-105 group-hover:opacity-100">
                                        </div>
                                        <span class="absolute -bottom-5 right-2 font-mono text-[9px] uppercase tracking-[0.2em] text-white/30" aria-hidden="true">SVC-{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }} / {{ $service->slug }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </section>
        @endif

        {{-- Portfolio --}}
        @if ($type === 'portfolio' && $portfolioProjects->isNotEmpty())
            <section class="relative py-24 md:py-32">
                <div class="max-w-[1440px] mx-auto px-4 md:px-8">
                    <x-public.space-heading
                        :index="$sectionNumber('portfolio')"
                        :label="$portfolioSection['subtitle'] ?? 'Portfolio'"
                        :title="$portfolioSection['title'] ?? 'Hasil Karya Kami'"
                    />
                    <div data-reveal data-reveal-delay="100" class="w-full flex flex-col sm:flex-row gap-2.5">
                        @foreach ($portfolioProjects as $project)
                            <a href="/portfolio/{{ $project->slug }}" class="gallery-panel relative overflow-hidden rounded-2xl block h-64 sm:h-[480px] bg-space-panel ring-1 ring-white/10">
                                @if ($project->thumbnail)
                                    <img src="{{ $project->thumbnail }}" alt="{{ $project->name }}" width="400" height="500" loading="lazy" class="absolute inset-0 h-full w-full object-cover">
                                @endif
                                <div class="absolute inset-0 pointer-events-none" style="background: linear-gradient(180deg, transparent 45%, rgb(var(--space-void)) 100%);"></div>
                                @if ($project->project_type === 'personal')
                                    <span class="absolute left-5 top-5 font-mono text-[10px] uppercase tracking-[0.18em] text-white border border-white/20 bg-black/40 backdrop-blur-sm px-3 py-1 rounded-full">Contoh Kapabilitas</span>
                                @endif
                                <div class="gallery-panel__label absolute left-5 bottom-5 flex items-center gap-3 text-white">
                                    <span class="h-8 w-px flex-shrink-0 bg-brand-400"></span>
                                    <span class="font-display font-semibold whitespace-nowrap drop-shadow">{{ $project->name }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                    <div class="mt-10 flex justify-end">
                        <a href="/portfolio" class="btn-space btn-space--glass">Lihat semua portfolio <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
                    </div>
                </div>
            </section>
        @endif

        {{-- Why Us --}}
        @if ($type === 'why_us' && $whyUs)
            {{--
                Why Us as a radar scan: a sweeping radar on the left with one
                "blip" per reason, and the reasons themselves as a staggered
                trajectory list on the right (each row indented differently,
                so the nodes trace a zig-zag path). Hovering a row locks the
                radar onto its blip; idle, the radar cycles through them.
            --}}
            @php
                $whyItems = array_values($whyUs['items'] ?? []);
                $whyCount = max(count($whyItems), 1);
                $blipRadii = [38, 24, 42, 30, 18, 34];
                $rowIndent = ['lg:ml-0', 'lg:ml-16', 'lg:ml-6', 'lg:ml-24', 'lg:ml-10', 'lg:ml-20'];
            @endphp
            <section class="relative py-24 md:py-36 overflow-hidden" data-radar>
                <div class="max-w-[1440px] mx-auto px-4 md:px-8 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-10">
                    <div class="lg:col-span-5">
                        <div class="lg:sticky lg:top-28">
                            <x-public.space-heading :index="$sectionNumber('why_us')" :label="$whyUs['subtitle'] ?? ''" :title="$whyUs['title'] ?? ''" class="!mb-10" />

                            <div class="radar relative w-[82%] sm:w-full max-w-[420px] mx-auto lg:mx-0" aria-hidden="true">
                                <div class="radar__sweep"></div>
                                @foreach ($whyItems as $index => $item)
                                    @php
                                        $angle = deg2rad(-90 + $index * (360 / $whyCount) + 22);
                                        $r = $blipRadii[$index % count($blipRadii)];
                                    @endphp
                                    <span
                                        class="radar__blip {{ cos($angle) < -0.05 ? 'radar__blip--left' : '' }} {{ $index === 0 ? 'is-active' : '' }}"
                                        data-radar-blip="{{ $index }}"
                                        style="left: {{ round(50 + $r * cos($angle), 2) }}%; top: {{ round(50 + $r * sin($angle), 2) }}%;"
                                    >
                                        <span class="radar__dot"></span>
                                        <span class="radar__label">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }} &middot; {{ \Illuminate\Support\Str::limit($item['title'] ?? '', 18) }}</span>
                                    </span>
                                @endforeach
                                <span class="radar__core"></span>
                                <span class="absolute left-1/2 -bottom-8 -translate-x-1/2 font-mono text-[9px] uppercase tracking-[0.3em] text-white/30 whitespace-nowrap">Scan &middot; {{ str_pad($whyCount, 2, '0', STR_PAD_LEFT) }} sinyal</span>
                            </div>
                        </div>
                    </div>

                    <ol class="lg:col-span-7 relative lg:pt-28">
                        @foreach ($whyItems as $index => $item)
                            <li
                                data-reveal
                                data-reveal-delay="{{ $index * 80 }}"
                                data-radar-item="{{ $index }}"
                                tabindex="0"
                                class="radar-row group relative flex gap-6 md:gap-8 py-8 md:py-10 outline-none {{ $rowIndent[$index % count($rowIndent)] }} {{ $index === 0 ? 'is-active' : '' }}"
                            >
                                <span class="radar-row__node relative z-10 mt-2 w-14 h-14 shrink-0 rounded-full border border-white/15 bg-space-void flex items-center justify-center font-mono text-[11px] text-white/50 transition-all duration-300">
                                    {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                </span>
                                <div class="min-w-0 max-w-xl">
                                    <h3 class="radar-row__title font-display font-semibold text-2xl md:text-4xl leading-[1.05] tracking-tight text-white/70 transition-colors duration-300 mb-3">{{ $item['title'] ?? '' }}</h3>
                                    <p class="text-white/50 leading-relaxed transition-colors duration-300 group-hover:text-white/70">{{ $item['description'] ?? '' }}</p>
                                    <span class="radar-row__bar block h-px mt-6 bg-gradient-to-r from-brand-400 to-transparent origin-left" aria-hidden="true"></span>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </section>
        @endif

        {{-- Process: orbit rings --}}
        @if ($type === 'process' && $process)
            <section class="relative py-24 md:py-36 overflow-hidden">
                <div class="orbits absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 pointer-events-none" aria-hidden="true">
                    @for ($n = 1; $n <= 6; $n++)
                        <span class="orbit" style="--i: {{ $n }}"></span>
                    @endfor
                </div>
                <div class="relative max-w-[1440px] mx-auto px-4 md:px-8">
                    <x-public.space-heading :index="$sectionNumber('process')" :label="$process['subtitle'] ?? ''" :title="$process['title'] ?? ''" center />
                    <div class="relative">
                        <div class="hidden md:block absolute top-8 left-[12.5%] right-[12.5%] border-t border-dashed border-white/20"></div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 md:gap-6">
                            @foreach (($process['steps'] ?? []) as $index => $step)
                                <div data-reveal data-reveal-delay="{{ $index * 100 }}" class="relative text-center px-4">
                                    <div class="process-node relative w-16 h-16 mx-auto mb-6 rounded-full border border-white/15 bg-space-deep flex items-center justify-center text-brand-400">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $processIcons[$index % 4] !!}</svg>
                                    </div>
                                    <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400 mb-3">Step {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</p>
                                    <h3 class="font-display font-semibold text-lg text-white tracking-tight mb-2">{{ $step['title'] ?? '' }}</h3>
                                    <p class="text-sm text-white/55 leading-relaxed max-w-xs mx-auto">{{ $step['description'] ?? '' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- Pricing teaser: a rising "price ladder" instead of 4 equal cells.
             Each category is a step, indented further than the last, with a
             light-beam bar whose length is its starting price relative to
             the most expensive category. --}}
        @if ($type === 'pricing')
            @php
                $maxCheapest = max(array_filter($cheapestByCategory) ?: [1]);
            @endphp
            <section class="relative py-24 md:py-32 overflow-hidden">
                <div class="max-w-[1440px] mx-auto px-4 md:px-8">
                    <x-public.space-heading :index="$sectionNumber('pricing')" :label="$pricingSection['subtitle'] ?? 'Investasi'" :title="$pricingSection['title'] ?? 'Paket Harga'" />
                    <ol class="relative space-y-3 md:space-y-4">
                        @foreach ($priceCategories as $index => $cat)
                            @php
                                $price = $cheapestByCategory[$cat['value']];
                                $ratio = $price ? max(0.18, $price / $maxCheapest) : 0.12;
                            @endphp
                            <li data-reveal data-reveal-delay="{{ $index * 90 }}" style="--step: {{ $index }}" class="ladder-step">
                                <a
                                    href="/harga?kategori={{ $cat['value'] }}"
                                    class="group relative flex flex-col md:flex-row md:items-center gap-4 md:gap-8 rounded-2xl border border-white/10 bg-white/[0.02] px-6 py-5 md:px-8 md:py-6 transition-colors duration-300 hover:border-brand-400/50 hover:bg-white/[0.04] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-400"
                                >
                                    <div class="flex items-center gap-4 md:w-64 shrink-0">
                                        <span class="w-11 h-11 rounded-full border border-white/15 flex items-center justify-center text-white/60 transition-colors group-hover:bg-brand-400 group-hover:text-space-void group-hover:border-brand-400">
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $categoryIcons[$cat['value']] !!}</svg>
                                        </span>
                                        <span>
                                            <span class="block font-mono text-[9px] uppercase tracking-[0.25em] text-white/35">Step {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                            <span class="block font-display font-semibold text-lg md:text-xl text-white tracking-tight">{{ $cat['label'] }}</span>
                                        </span>
                                    </div>
                                    <div class="flex-1 flex items-center gap-4 min-w-0">
                                        <span class="relative h-[3px] rounded-full bg-white/10 flex-1 overflow-hidden" aria-hidden="true">
                                            <span class="ladder-beam absolute inset-y-0 left-0 rounded-full" style="width: {{ round($ratio * 100) }}%"></span>
                                        </span>
                                        <span class="shrink-0 text-right">
                                            <span class="block font-mono text-[9px] uppercase tracking-[0.25em] text-white/35">Mulai dari</span>
                                            <span class="block font-display text-xl md:text-2xl text-brand-300 tabular-nums">{{ $price ? \App\Support\PriceFormatter::idr($price) : 'Segera Hadir' }}</span>
                                        </span>
                                        <span class="hidden md:inline-flex w-10 h-10 shrink-0 rounded-full border border-white/15 items-center justify-center text-white transition-transform duration-300 group-hover:-rotate-45" aria-hidden="true">&rarr;</span>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </section>
        @endif

        {{-- Testimonials: one transmission blown up as giant type, the rest as
             small tilted "signal cards" scattered down the right side. --}}
        @if ($type === 'testimonials' && $testimonials->isNotEmpty())
            @php
                $leadQuote = $testimonials->first();
                $otherQuotes = $testimonials->slice(1)->values();
                $tilts = ['lg:rotate-[2deg] lg:translate-x-6', 'lg:-rotate-[1.5deg] lg:-translate-x-4', 'lg:rotate-[1deg] lg:translate-x-10'];
            @endphp
            <section class="relative max-w-[1440px] mx-auto px-4 md:px-8 py-24 md:py-32 overflow-hidden">
                <x-public.space-heading
                    :index="$sectionNumber('testimonials')"
                    :label="$testimonialsSection['subtitle'] ?? 'Testimoni'"
                    :title="$testimonialsSection['title'] ?? 'Apa Kata Klien Kami'"
                />
                <div class="relative grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-10 items-start">
                    <span class="absolute -left-4 -top-16 font-display font-extrabold text-[16rem] leading-none text-transparent [-webkit-text-stroke:1px_rgb(var(--lime-400)/0.25)] select-none pointer-events-none" aria-hidden="true">&ldquo;</span>

                    <figure data-reveal class="relative lg:col-span-7 lg:pt-16">
                        <div class="flex items-center gap-3 mb-8 font-mono text-[10px] uppercase tracking-[0.25em] text-white/40">
                            <span class="w-2 h-2 rounded-full bg-brand-400 animate-pulse motion-reduce:animate-none" aria-hidden="true"></span>
                            Transmisi klien
                            <span class="text-brand-400" aria-label="Rating {{ $leadQuote->rating }} dari 5">{{ str_repeat('★', (int) $leadQuote->rating) }}</span>
                        </div>
                        <blockquote class="font-display font-semibold text-2xl md:text-4xl lg:text-[2.75rem] leading-[1.18] tracking-tight text-white [text-wrap:balance]">
                            {{ $leadQuote->testimonial }}
                        </blockquote>
                        <figcaption class="mt-10 flex items-center gap-4">
                            <span class="w-12 h-px bg-brand-400" aria-hidden="true"></span>
                            <span>
                                <span class="block font-display font-semibold text-white">{{ $leadQuote->name }}</span>
                                <span class="block font-mono text-[10px] uppercase tracking-[0.18em] text-white/40 mt-1">{{ $leadQuote->position }}@if($leadQuote->company) / {{ $leadQuote->company }}@endif</span>
                            </span>
                        </figcaption>
                    </figure>

                    <div class="lg:col-span-5 space-y-6 lg:space-y-0">
                        @foreach ($otherQuotes as $i => $t)
                            <div data-reveal data-reveal-delay="{{ ($i + 1) * 120 }}">
                            <figure class="relative rounded-2xl border border-white/10 bg-space-deep/80 backdrop-blur-md p-6 md:p-7 shadow-[0_30px_60px_-30px_rgb(0_0_0/0.6)] transition-transform duration-500 hover:!rotate-0 {{ $tilts[$i % count($tilts)] }} {{ $i === 0 ? 'lg:mt-24' : 'lg:-mt-2' }}">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="font-mono text-[9px] uppercase tracking-[0.25em] text-brand-400">Sinyal {{ str_pad($i + 2, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="font-mono text-[10px] text-white/40" aria-label="Rating {{ $t->rating }} dari 5">{{ str_repeat('★', (int) $t->rating) }}</span>
                                </div>
                                <blockquote class="text-white/80 leading-relaxed mb-5">&ldquo;{{ $t->testimonial }}&rdquo;</blockquote>
                                <figcaption>
                                    <span class="block font-display font-semibold text-sm text-white">{{ $t->name }}</span>
                                    <span class="block font-mono text-[10px] uppercase tracking-[0.18em] text-white/40 mt-1">{{ $t->position }}@if($t->company) / {{ $t->company }}@endif</span>
                                </figcaption>
                            </figure>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- FAQ --}}
        @if ($type === 'faq')
            <section class="py-24 md:py-32">
                <div class="max-w-[1440px] mx-auto px-4 md:px-8 grid grid-cols-1 lg:grid-cols-12 gap-10">
                    <div class="lg:col-span-5">
                        <x-public.space-heading
                            :index="$sectionNumber('faq')"
                            :label="$faqSection['subtitle'] ?? 'FAQ'"
                            :title="$faqSection['title'] ?? 'Pertanyaan yang Sering Diajukan'"
                        />
                    </div>
                    <div class="lg:col-span-7 border-t border-white/10">
                        @foreach ($faqs as $index => $faq)
                            <div class="border-b border-white/10">
                                <button
                                    type="button"
                                    data-faq-toggle
                                    aria-expanded="false"
                                    aria-controls="home-faq-answer-{{ $faq->id }}"
                                    class="group w-full flex items-center gap-5 py-6 text-left"
                                >
                                    <span class="font-mono text-[10px] tracking-[0.2em] text-white/35 shrink-0">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="flex-1 font-semibold text-white/85 transition-colors group-hover:text-brand-300">{{ $faq->question }}</span>
                                    <span data-faq-icon aria-hidden="true" class="w-8 h-8 shrink-0 rounded-full border border-white/15 flex items-center justify-center text-brand-400 transition-transform duration-300">+</span>
                                </button>
                                <div id="home-faq-answer-{{ $faq->id }}" class="hidden pb-6 pl-10 pr-12 text-white/55 leading-relaxed">{{ $faq->answer }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        {{-- Blog --}}
        @if ($type === 'blog' && $latestPosts->isNotEmpty())
            <section class="py-24 md:py-32">
                <div class="max-w-[1440px] mx-auto px-4 md:px-8">
                    <x-public.space-heading
                        :index="$sectionNumber('blog')"
                        :label="$blogSection['subtitle'] ?? 'Blog'"
                        :title="$blogSection['title'] ?? 'Artikel & Insight Terbaru'"
                    />
                    {{--
                        Editorial layout instead of an even 3-up grid: a giant
                        rotated "JOURNAL" spine, one lead story as a tall tilted
                        poster, and the rest as a staggered transmission log.
                    --}}
                    @php
                        $leadPost = $latestPosts->first();
                        $logPosts = $latestPosts->slice(1)->values();
                        $postDate = fn ($p) => $p->published_at ? \Carbon\Carbon::parse($p->published_at)->locale('id')->translatedFormat('d M Y') : '';
                    @endphp
                    <div class="relative grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start">
                        <span class="hidden xl:block absolute -left-2 top-0 [writing-mode:vertical-rl] rotate-180 font-display font-extrabold uppercase text-[7rem] leading-none tracking-tight text-transparent [-webkit-text-stroke:1px_rgb(var(--ink)/0.08)] select-none pointer-events-none" aria-hidden="true">Journal</span>

                        <article data-reveal class="group lg:col-span-7 xl:col-start-2">
                            <a href="/blog/{{ $leadPost->slug }}" data-tilt class="tilt-card relative block rounded-3xl overflow-hidden ring-1 ring-white/10 bg-space-panel aspect-[4/5] md:aspect-[5/4]">
                                @if ($leadPost->featured_image)
                                    <img src="{{ $leadPost->featured_image }}" alt="{{ $leadPost->title }}" width="800" height="640" loading="lazy" class="absolute inset-0 w-full h-full object-cover opacity-80 transition-all duration-700 group-hover:scale-105 group-hover:opacity-100">
                                @endif
                                <div class="absolute inset-0 bg-gradient-to-t from-space-void via-space-void/40 to-transparent" aria-hidden="true"></div>
                                <span class="absolute top-5 left-5 font-mono text-[10px] uppercase tracking-[0.2em] bg-brand-400 text-space-void px-3 py-1 rounded-full">Log &middot; 01</span>
                                <span class="absolute top-5 right-5 w-11 h-11 rounded-full border border-white/25 bg-black/30 backdrop-blur-md flex items-center justify-center text-white transition-transform duration-500 group-hover:-rotate-45" aria-hidden="true">&rarr;</span>
                                <div class="absolute inset-x-0 bottom-0 p-6 md:p-10">
                                    <div class="flex flex-wrap items-center gap-3 font-mono text-[10px] uppercase tracking-[0.2em] text-white/60 mb-4">
                                        @if ($leadPost->category)
                                            <span class="text-brand-300">{{ $leadPost->category->name }}</span>
                                        @endif
                                        <span>{{ $postDate($leadPost) }}</span>
                                    </div>
                                    <h3 class="font-display font-semibold text-2xl md:text-4xl leading-[1.08] tracking-tight text-white max-w-xl [text-wrap:balance]">{{ $leadPost->title }}</h3>
                                </div>
                            </a>
                        </article>

                        <div class="lg:col-span-5 xl:col-span-4 lg:pt-24">
                            <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-white/40 mb-6 flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full bg-brand-400 animate-pulse motion-reduce:animate-none" aria-hidden="true"></span>
                                Transmisi terbaru
                            </p>
                            <ol class="space-y-2">
                                @foreach ($logPosts as $i => $post)
                                    <li data-reveal data-reveal-delay="{{ ($i + 1) * 100 }}" class="{{ $i % 2 === 1 ? 'lg:ml-10' : '' }}">
                                        <a href="/blog/{{ $post->slug }}" class="group relative flex items-center gap-5 rounded-2xl border border-transparent p-4 -mx-4 transition-colors hover:border-white/10 hover:bg-white/[0.03]">
                                            <span class="font-display font-extrabold text-5xl leading-none tabular-nums text-transparent [-webkit-text-stroke:1px_rgb(var(--ink)/0.22)] transition-all duration-300 group-hover:[-webkit-text-stroke:1px_rgb(var(--lime-400)/0.9)]" aria-hidden="true">{{ str_pad($i + 2, 2, '0', STR_PAD_LEFT) }}</span>
                                            <span class="min-w-0 flex-1">
                                                <span class="block font-mono text-[10px] uppercase tracking-[0.18em] text-white/40 mb-1.5">
                                                    @if ($post->category)<span class="text-brand-400">{{ $post->category->name }}</span> &middot; @endif{{ $postDate($post) }}
                                                </span>
                                                <span class="block font-display font-semibold text-white tracking-tight leading-snug line-clamp-2 transition-colors group-hover:text-brand-300">{{ $post->title }}</span>
                                            </span>
                                            @if ($post->featured_image)
                                                <img src="{{ $post->featured_image }}" alt="" aria-hidden="true" width="64" height="64" loading="lazy" class="w-16 h-16 rounded-full object-cover shrink-0 ring-1 ring-white/15 opacity-60 grayscale transition-all duration-500 group-hover:opacity-100 group-hover:grayscale-0 group-hover:scale-110">
                                            @endif
                                        </a>
                                    </li>
                                @endforeach
                            </ol>
                            <div class="mt-10 {{ $logPosts->isEmpty() ? '' : 'lg:ml-10' }}">
                                <a href="/blog" class="btn-space btn-space--glass">Lihat semua artikel <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        {{-- Final CTA (lead capture form) --}}
        @if ($type === 'cta' && $ctaSection)
            <section class="contact-cta relative py-24 md:py-32 overflow-hidden">
                <div class="max-w-[1440px] mx-auto px-4 md:px-8 relative">
                  <div class="relative overflow-hidden rounded-3xl border border-white/10 bg-space-deep/70 backdrop-blur-xl px-6 py-12 md:px-14 md:py-16">
                    <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
                    <div class="flex flex-col md:flex-row items-center gap-10 md:gap-20">
                        <div class="w-full md:w-2/5 flex justify-center flex-shrink-0">
                            @if ($mascots['cta_kontak']['image_path'] ?? null)
                                <img src="{{ $mascots['cta_kontak']['image_path'] }}" alt="{{ $mascots['cta_kontak']['alt_text'] ?? '' }}" class="w-full max-w-sm object-contain">
                            @else
                                <svg viewBox="0 0 360 320" class="w-full max-w-sm" role="img" aria-label="Ilustrasi kirim pesan">
                                    <g fill="#a3e635">
                                        <circle id="dot1" class="dot" cx="60" cy="60" r="3" />
                                        <circle id="dot2" class="dot" cx="300" cy="50" r="2.5" />
                                        <circle id="dot3" class="dot" cx="320" cy="140" r="3" />
                                        <circle id="dot4" class="dot" cx="30" cy="165" r="2.5" />
                                        <circle id="dot5" class="dot" cx="280" cy="235" r="3" />
                                    </g>
                                    <circle cx="180" cy="175" r="120" fill="#65a30d" opacity="0.12" />
                                    <g class="ambient-float-lg">
                                        <polygon points="90,205 285,120 195,168" fill="#a3e635" />
                                        <polygon points="195,168 285,120 215,235" fill="#4d7c0f" />
                                        <line x1="195" y1="168" x2="285" y2="120" stroke="#1a2e05" stroke-width="3" stroke-linecap="round" />
                                        <polygon points="90,205 285,120 215,235 195,168" fill="none" stroke="#1a2e05" stroke-width="4" stroke-linejoin="round" stroke-linecap="round" />
                                    </g>
                                </svg>
                            @endif
                        </div>

                        <div class="w-full md:w-3/5">
                            @if (!empty($ctaSection['subtitle']))
                                <p data-reveal class="flex items-center gap-3 font-mono text-[10px] md:text-[11px] uppercase tracking-[0.25em] text-white/50 mb-5">
                                    <span class="text-brand-400">[ {{ str_pad($sectionNumber('cta'), 2, '0', STR_PAD_LEFT) }} ]</span>
                                    {{ $ctaSection['subtitle'] }}
                                </p>
                            @endif
                            <h2 data-reveal data-reveal-delay="80" class="font-display font-semibold text-3xl md:text-5xl leading-[1.05] tracking-tight text-white mb-5 [text-wrap:balance]">
                                {{ $ctaSection['title'] ?? 'Siap Membangun Website Impian Anda?' }}
                            </h2>
                            @if (!empty($ctaSection['description']))
                                <p data-reveal data-reveal-delay="120" class="text-gray-300 mb-8">{{ $ctaSection['description'] }}</p>
                            @endif

                            <div id="home-cta-form-success" class="hidden flex items-start gap-3 bg-white/5 border border-white/10 rounded-lg p-5">
                                <span class="text-brand-400 text-xl leading-none shrink-0 mt-0.5" aria-hidden="true">&#10022;</span>
                                <div>
                                    <p class="font-semibold text-white mb-1">Terima kasih!</p>
                                    <p class="text-sm text-gray-300">Tim kami akan menghubungi Anda dalam 1x24 jam via WhatsApp.</p>
                                </div>
                            </div>

                            <form data-lead-form="#home-cta-form" id="home-cta-form" action="{{ route('leads.store') }}" method="POST" class="space-y-4">
                                @csrf
                                <input type="hidden" name="source" value="cta_home">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <div class="lead-field">
                                            <svg class="lead-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="8" r="4" /><path d="M4 20c0-4 4-6 8-6s8 2 8 6" /></svg>
                                            <input name="name" type="text" placeholder="Nama" required class="field-input">
                                        </div>
                                        <p data-field-error="name" class="field-error"></p>
                                    </div>
                                    <div>
                                        <div class="lead-field">
                                            <svg class="lead-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92Z" /></svg>
                                            <input name="whatsapp" type="text" placeholder="Nomor WhatsApp" required class="field-input">
                                        </div>
                                        <p data-field-error="whatsapp" class="field-error"></p>
                                    </div>
                                </div>
                                <div>
                                    <div class="lead-field">
                                        <svg class="lead-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2" /><path d="m22 6-10 7L2 6" /></svg>
                                        <input name="email" type="email" placeholder="E-mail (opsional)" class="field-input">
                                    </div>
                                    <p data-field-error="email" class="field-error"></p>
                                </div>
                                <div>
                                    <div class="lead-field lead-field--textarea">
                                        <svg class="lead-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" /></svg>
                                        <textarea name="message" rows="3" placeholder="Pesan (opsional)" class="field-input"></textarea>
                                    </div>
                                </div>
                                <button type="submit" data-lead-submit class="lead-submit">Kirim Pesan</button>
                            </form>
                        </div>
                    </div>
                  </div>
                </div>
            </section>
        @endif

    @endforeach
</x-layouts.public>
