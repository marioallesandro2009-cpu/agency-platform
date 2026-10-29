@php
    $pad = fn ($n) => str_pad($n, 2, '0', STR_PAD_LEFT);
    $values = array_values($about['values'] ?? []);

    // Default icons for the 4 known values until an admin picks one; check
    // style icons already saved in the DB are swapped out (no check marks).
    $valueIconFallback = ['Transparansi' => 'compass', 'Kualitas' => 'star', 'Kolaboratif' => 'users', 'Tepat Waktu' => 'clock'];
    $valueIcon = function ($value) use ($valueIconFallback) {
        $icon = in_array($value['icon'] ?? null, ['check-circle', 'shield-check'], true) ? null : ($value['icon'] ?? null);
        return $icon ?: ($valueIconFallback[$value['title'] ?? ''] ?? 'sparkles');
    };

    $heroImage = ($about['image'] ?? null) ?: ($mascots['hero_tentang_kami']['image_path'] ?? null);

    // "[ 0X ]" numbers for whichever optional sections this page has.
    $sectionKeys = array_keys(array_filter([
        'profile' => !empty($about['description']),
        'mission' => !empty($about['mission']) || !empty($about['vision']),
        'values' => !empty($values),
        'tech' => $techStacks->isNotEmpty(),
    ]));
    $sectionNumbers = [];
    foreach ($sectionKeys as $i => $key) {
        $sectionNumbers[$key] = $pad($i + 1);
    }

    // Star-chart stats: each figure gets its own size and vertical drift.
    $statSize = ['text-6xl md:text-8xl', 'text-4xl md:text-5xl', 'text-5xl md:text-7xl', 'text-4xl md:text-6xl'];
    $statDrift = ['md:-translate-y-10', 'md:translate-y-12', 'md:-translate-y-4', 'md:translate-y-16'];

    $tickerWords = ['Terpercaya', 'Cepat', 'Profesional', 'Transparan', 'Kolaboratif'];
@endphp

<x-layouts.public
    theme="space"
    seo-title="Tentang Kami"
    :seo-description="($about['description'] ?? null) ?: 'Kenali lebih dekat tim dan visi misi kami dalam membangun website untuk bisnis Anda.'"
>
    <x-public.space-page-header
        :eyebrow="($about['subtitle'] ?? null) ?: 'Siapa Kami'"
        code="ABT"
        :title="($about['title'] ?? null) ?: 'Tentang Kami'"
    />

    {{-- Profile + stats --}}
    @if (!empty($about['description']))
        <section class="max-w-[1440px] mx-auto px-4 md:px-8 py-20 md:py-28">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
                <div data-reveal="left" class="lg:col-span-6 order-2 lg:order-1">
                    @if ($mascots['intro_tentang_kami']['image_path'] ?? null)
                        <img src="{{ \App\Support\Img::url($mascots['intro_tentang_kami']['image_path'], 320) }}" alt="{{ $mascots['intro_tentang_kami']['alt_text'] ?? '' }}" width="80" height="80" loading="lazy" class="w-20 h-20 object-contain mb-6">
                    @endif
                    <p class="flex items-center gap-4 font-mono text-[10px] md:text-[11px] uppercase tracking-[0.25em] text-white/45 mb-6">
                        <span class="text-brand-400">[ {{ $sectionNumbers['profile'] }} ]</span>
                        Profil kami
                        <span class="h-px flex-1 bg-gradient-to-r from-white/20 to-transparent"></span>
                    </p>
                    <p class="text-lg md:text-xl leading-relaxed text-white/75 [text-wrap:pretty] first-line:text-white">{{ $about['description'] }}</p>
                </div>
                <div data-reveal="right" data-reveal-delay="100" class="lg:col-span-6 order-1 lg:order-2">
                    <div class="hud-frame relative p-2">
                        <div class="aspect-[4/3] rounded-lg overflow-hidden bg-space-panel">
                            @if ($heroImage)
                                <img
                                    src="{{ \App\Support\Img::url($heroImage, 960) }}"
                                    srcset="{{ \App\Support\Img::srcset($heroImage, [480, 960, 1280, 1600]) }}"
                                    sizes="(min-width: 1024px) 50vw, 100vw"
                                    alt="{{ ($about['title'] ?? null) ?: 'Tentang Kami' }}"
                                    width="700"
                                    height="525"
                                    class="w-full h-full object-cover"
                                >
                            @else
                                <div class="w-full h-full flex items-center justify-center font-mono text-[10px] uppercase tracking-[0.25em] text-white/35" aria-hidden="true">Crew &middot; {{ $settings['general']['website_name'] ?? '' }}</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats as a star chart: numbers at different sizes and heights,
                 strung on one dashed orbit line, instead of four equal cells. --}}
            @if (!empty($stats))
                <div class="relative mt-20 md:mt-28">
                    <svg class="hidden md:block absolute inset-x-0 top-1/2 -translate-y-1/2 w-full h-40 pointer-events-none" viewBox="0 0 1000 160" preserveAspectRatio="none" aria-hidden="true">
                        <path d="M0 120 C 200 20, 380 150, 560 70 S 860 10, 1000 90" fill="none" stroke="rgb(var(--ink) / 0.15)" stroke-width="1" stroke-dasharray="4 6" vector-effect="non-scaling-stroke" />
                    </svg>
                    <dl class="relative grid grid-cols-2 md:flex md:items-center md:justify-between gap-y-10 gap-x-6">
                        @foreach ($stats as $index => $stat)
                            {{-- drift on the outer box, reveal (its own transform) on the inner one --}}
                            <div class="relative {{ $statDrift[$index % count($statDrift)] }}">
                                <div data-reveal data-reveal-delay="{{ $index * 100 }}">
                                    <span class="absolute -left-3 top-1 w-1.5 h-1.5 rounded-full bg-brand-400 shadow-[0_0_12px_rgb(var(--lime-400))]" aria-hidden="true"></span>
                                    <dt class="font-mono text-[10px] uppercase tracking-[0.2em] text-white/45 mb-2">/{{ $pad($index + 1) }} &mdash; {{ $stat['label'] }}</dt>
                                    <dd class="font-display font-semibold text-brand-300 tabular-nums tracking-tight leading-none {{ $statSize[$index % count($statSize)] }}">{{ $stat['value'] }}</dd>
                                </div>
                            </div>
                        @endforeach
                    </dl>
                </div>
            @endif
        </section>
    @endif

    {{-- Misi & Visi: the words themselves are the visual, as giant outlined type --}}
    @if (!empty($about['mission']) || !empty($about['vision']))
        <section class="relative py-20 md:py-28 border-y border-white/10 overflow-hidden">
            <div class="max-w-[1440px] mx-auto px-4 md:px-8">
                <p class="flex items-center gap-4 font-mono text-[10px] md:text-[11px] uppercase tracking-[0.25em] text-white/45 mb-12">
                    <span class="text-brand-400">[ {{ $sectionNumbers['mission'] }} ]</span>
                    Arah kami
                    <span class="h-px flex-1 bg-gradient-to-r from-white/20 to-transparent"></span>
                </p>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-px rounded-2xl overflow-hidden border border-white/10 bg-white/10">
                    @if (!empty($about['mission']))
                        <div data-reveal="left" class="relative bg-space-void p-8 md:p-12 overflow-hidden">
                            <span class="block font-display font-extrabold uppercase leading-none tracking-tight text-transparent [-webkit-text-stroke:1px_rgb(var(--ink)/0.25)] text-[clamp(3.5rem,9vw,8rem)] mb-8" aria-hidden="true">Misi</span>
                            <h2 class="font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400 mb-4">Misi kami</h2>
                            <p class="text-white/75 leading-relaxed text-lg md:text-xl">{{ $about['mission'] }}</p>
                        </div>
                    @endif
                    @if (!empty($about['vision']))
                        <div data-reveal="right" data-reveal-delay="100" class="relative bg-space-deep p-8 md:p-12 overflow-hidden">
                            <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
                            <span class="relative block font-display font-extrabold uppercase leading-none tracking-tight text-transparent [-webkit-text-stroke:1px_rgb(var(--lime-400)/0.55)] text-[clamp(3.5rem,9vw,8rem)] mb-8" aria-hidden="true">Visi</span>
                            <h2 class="relative font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400 mb-4">Visi kami</h2>
                            <p class="relative text-white/85 leading-relaxed text-lg md:text-xl">{{ $about['vision'] }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="about-ticker mt-16 md:mt-20" aria-hidden="true">
                <div class="about-ticker__track">
                    @for ($n = 0; $n < 2; $n++)
                        <span class="flex items-center">
                            @foreach (array_merge($tickerWords, $tickerWords) as $word)
                                <span class="about-ticker__word">{{ $word }}</span>
                                <span class="about-ticker__sep">&#10022;</span>
                            @endforeach
                        </span>
                    @endfor
                </div>
            </div>
        </section>
    @endif

    {{-- Values as a "core reactor" console: command list (auto-cycling, with
         a dwell-time progress bar) + a big HUD display of the active value.
         Driven by blade-app.js initValuesConsole. --}}
    @if (!empty($values))
        <section class="max-w-[1440px] mx-auto px-4 md:px-8 py-20 md:py-28" data-values-console>
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                <div class="lg:col-span-5">
                    <x-public.space-heading :index="$sectionNumbers['values']" label="Nilai" title="Nilai-Nilai Kami" />
                    <ul class="space-y-1" role="tablist" aria-label="Nilai-nilai kami">
                        @foreach ($values as $index => $value)
                            <li class="{{ $index % 2 === 1 ? 'lg:ml-8' : '' }}">
                                <button
                                    type="button"
                                    role="tab"
                                    id="value-tab-{{ $index }}"
                                    aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                                    aria-controls="value-panel-{{ $index }}"
                                    data-value-tab="{{ $index }}"
                                    class="value-tab group relative w-full text-left flex items-baseline gap-4 py-4 px-1 focus-visible:outline-none"
                                >
                                    <span class="value-tab__num font-mono text-[10px] tracking-[0.2em]">&gt; {{ $pad($index + 1) }}</span>
                                    <span class="value-tab__title font-display font-semibold uppercase tracking-tight transition-all duration-300 text-2xl md:text-3xl">{{ $value['title'] }}</span>
                                    <span class="absolute left-0 right-0 bottom-0 h-px bg-white/10 overflow-hidden" aria-hidden="true">
                                        <span class="value-progress block h-full bg-brand-400"></span>
                                    </span>
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="lg:col-span-7 relative">
                    <div class="hud-frame relative p-2">
                        <div class="relative overflow-hidden rounded-xl bg-space-deep min-h-[420px] md:min-h-[480px] p-8 md:p-12 flex flex-col justify-end [container-type:inline-size]">
                            <div class="absolute -right-24 -top-24 w-[420px] h-[420px] pointer-events-none" aria-hidden="true">
                                @for ($n = 1; $n <= 4; $n++)
                                    <span class="absolute rounded-full border {{ $n % 2 ? 'border-white/10 border-dashed' : 'border-brand-400/20' }}" style="inset: {{ $n * 11 }}%"></span>
                                @endfor
                                <span class="value-orbit absolute inset-[11%]">
                                    @foreach ($values as $index => $value)
                                        <span data-value-icon="{{ $index }}" class="{{ $index === 0 ? '' : 'hidden' }} absolute left-1/2 -top-6 -translate-x-1/2 w-12 h-12 rounded-full bg-brand-400 text-space-void flex items-center justify-center shadow-[0_0_40px_-4px_rgb(var(--lime-400)/0.8)]">
                                            <x-public.icon :name="$valueIcon($value)" />
                                        </span>
                                    @endforeach
                                </span>
                            </div>
                            <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
                            <span class="absolute top-6 left-8 font-mono text-[10px] uppercase tracking-[0.25em] text-white/35">Core value &middot; <span data-value-counter>01</span> / {{ $pad(count($values)) }}</span>

                            @foreach ($values as $index => $value)
                                <div id="value-panel-{{ $index }}" role="tabpanel" aria-labelledby="value-tab-{{ $index }}" data-value-panel="{{ $index }}" class="value-panel relative {{ $index === 0 ? 'is-active' : 'hidden' }}">
                                    <p class="font-display font-extrabold uppercase leading-[0.9] tracking-tight text-transparent [-webkit-text-stroke:1px_rgb(var(--lime-400)/0.6)] text-[min(8cqw,5.5rem)] mb-6 whitespace-nowrap" aria-hidden="true">{{ $value['title'] }}</p>
                                    <h3 class="sr-only">{{ $value['title'] }}</h3>
                                    <p class="text-lg md:text-xl text-white/75 leading-relaxed max-w-lg">{{ $value['description'] ?? '' }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- Tech stack: a slow marquee of logo chips (pure CSS, no animation library). --}}
    @if ($techStacks->isNotEmpty())
        <section class="max-w-[1440px] mx-auto px-4 md:px-8 pb-8">
            <div class="rounded-2xl border border-white/10 bg-white/[0.02] px-6 py-12 md:px-12 md:py-14 overflow-hidden">
                <x-public.space-heading :index="$sectionNumbers['tech']" label="Stack" title="Teknologi yang Kami Gunakan" center />
                <div class="tech-marquee" aria-label="Teknologi yang kami gunakan">
                    <div class="tech-marquee__track">
                        @for ($n = 0; $n < 2; $n++)
                            <ul class="flex items-center gap-6 pr-6" @if ($n === 1) aria-hidden="true" @endif>
                                @foreach ($techStacks as $tech)
                                    <li>
                                        @if ($tech->link_url && $n === 0)
                                            <a href="{{ $tech->link_url }}" target="_blank" rel="noreferrer noopener" class="tech-chip" title="{{ $tech->name }}">
                                                <img src="{{ $tech->icon_url }}" alt="{{ $tech->name }}" width="48" height="48" loading="lazy">
                                            </a>
                                        @else
                                            <span class="tech-chip" title="{{ $tech->name }}">
                                                <img src="{{ $tech->icon_url }}" alt="{{ $n === 0 ? $tech->name : '' }}" width="48" height="48" loading="lazy">
                                            </span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endfor
                    </div>
                </div>
            </div>
        </section>
    @endif

    <div class="pt-24 md:pt-32">
        <x-public.contact-cta
            form-id="about-cta-form"
            title="Siap Bekerja Sama dengan Kami?"
            description="Ceritakan kebutuhan website Anda, tim kami akan bantu wujudkan dalam waktu singkat."
        />
    </div>
</x-layouts.public>
