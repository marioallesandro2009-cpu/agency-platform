@php
    $whyWebsite = [
        ['title' => 'Kredibilitas', 'text' => 'Website profesional membangun kepercayaan calon pelanggan lebih cepat dibanding media sosial saja.'],
        ['title' => 'Jangkauan Lebih Luas', 'text' => 'Ditemukan lewat pencarian Google, bukan cuma dari orang yang sudah follow akun Anda.'],
        ['title' => 'Kendali Penuh', 'text' => 'Konten, tampilan, dan data pelanggan sepenuhnya milik Anda, tidak terikat platform pihak ketiga.'],
    ];
@endphp

<x-layouts.public
    theme="space"
    seo-title="Jasa Website"
    seo-description="Kami membangun website yang cepat, modern, dan sesuai kebutuhan bisnis Anda — dari UMKM hingga sistem custom."
>
    <x-public.space-page-header
        eyebrow="Jasa Website"
        code="SVC"
        title="Jasa Website Profesional"
        subtitle="Kami membangun website yang cepat, modern, dan sesuai kebutuhan bisnis Anda — dari UMKM hingga sistem custom."
    >
        <div class="flex flex-wrap gap-3">
            <a href="/konsultasi" class="btn-space btn-space--lg btn-space--pill">Konsultasi Gratis <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
            <a href="/harga" class="btn-space btn-space--lg btn-space--glass">Lihat Harga <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
        </div>
    </x-public.space-page-header>

    {{-- Service index: big rows, outlined numeral, image peeks in on hover --}}
    <section class="max-w-[1440px] mx-auto px-4 md:px-8 py-24 md:py-32">
        <x-public.space-heading index="01" label="Layanan" title="Jenis Layanan Website Kami" />
        <div class="border-t border-white/10">
            @foreach ($services as $index => $service)
                <a
                    href="/{{ $service->slug }}"
                    data-reveal
                    data-reveal-delay="{{ min($index * 80, 240) }}"
                    class="group relative grid grid-cols-12 items-center gap-4 md:gap-8 border-b border-white/10 py-8 md:py-10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-brand-400"
                >
                    <span class="col-span-2 md:col-span-1 font-display font-extrabold text-3xl md:text-5xl leading-none tabular-nums text-transparent [-webkit-text-stroke:1px_rgb(var(--ink)/0.25)] transition-all duration-500 group-hover:[-webkit-text-stroke:1px_rgb(var(--lime-400)/0.9)]" aria-hidden="true">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <div class="col-span-10 md:col-span-6">
                        <h3 class="font-display font-semibold text-2xl md:text-4xl text-white tracking-tight transition-transform duration-500 group-hover:translate-x-2">{{ $service->name }}</h3>
                        <p class="text-sm md:text-base text-white/50 mt-2 max-w-lg">{{ $service->short_description }}</p>
                    </div>
                    <div class="hidden md:block md:col-span-4">
                        <div class="aspect-[16/9] rounded-xl overflow-hidden bg-space-panel ring-1 ring-white/10 opacity-40 grayscale transition-all duration-500 group-hover:opacity-100 group-hover:grayscale-0">
                            @if ($service->image)
                                <img src="{{ $service->image }}" alt="" aria-hidden="true" width="320" height="180" loading="lazy" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                            @else
                                {{-- No image uploaded yet: HUD placeholder instead of an empty box --}}
                                <div class="w-full h-full bg-grid-subtle flex items-center justify-center font-mono text-[10px] uppercase tracking-[0.25em] text-white/35" aria-hidden="true">
                                    SVC-{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}
                                </div>
                            @endif
                        </div>
                    </div>
                    <span class="hidden md:flex md:col-span-1 justify-end" aria-hidden="true">
                        <span class="w-12 h-12 rounded-full border border-white/15 flex items-center justify-center text-white transition-all duration-300 group-hover:bg-brand-400 group-hover:border-brand-400 group-hover:text-space-void group-hover:-rotate-45">&rarr;</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Why a website --}}
    <section class="relative py-24 md:py-32 overflow-hidden border-y border-white/10">
        <div class="orbits absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 pointer-events-none" aria-hidden="true">
            @for ($n = 1; $n <= 6; $n++)
                <span class="orbit" style="--i: {{ $n }}"></span>
            @endfor
        </div>
        <div class="relative max-w-[1440px] mx-auto px-4 md:px-8">
            <x-public.space-heading index="02" label="Alasan" title="Kenapa Bisnis Anda Butuh Website" center />
            {{-- Three reasons as big statements drifting across the page
                 (left, centre-right, far right), each anchored by a giant
                 outline numeral, instead of three equal cards. --}}
            @php $whyAlign = ['lg:mr-auto', 'lg:mx-auto lg:translate-x-[18%]', 'lg:ml-auto']; @endphp
            <ol class="space-y-14 md:space-y-10">
                @foreach ($whyWebsite as $i => $item)
                    <li class="group relative max-w-xl {{ $whyAlign[$i % count($whyAlign)] }}">
                        <span class="absolute -left-4 md:-left-16 -top-10 font-display font-extrabold text-[7rem] md:text-[9rem] leading-none text-transparent [-webkit-text-stroke:1px_rgb(var(--ink)/0.1)] transition-all duration-500 group-hover:[-webkit-text-stroke:1px_rgb(var(--lime-400)/0.7)] pointer-events-none select-none" aria-hidden="true">{{ $i + 1 }}</span>
                        <div data-reveal="{{ $i % 2 === 1 ? 'right' : 'left' }}" data-reveal-delay="{{ $i * 100 }}" class="relative pl-6 border-l border-brand-400/60">
                            <h3 class="font-display font-semibold text-2xl md:text-4xl text-white tracking-tight mb-3">{{ $item['title'] }}</h3>
                            <p class="text-white/60 leading-relaxed md:text-lg">{{ $item['text'] }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    @if ($portfolioProjects->isNotEmpty())
        <section class="max-w-[1440px] mx-auto px-4 md:px-8 py-24 md:py-32">
            <x-public.space-heading index="03" label="Portfolio" title="Contoh Hasil Kerja Kami" />
            {{-- Uneven film-strip: first project large, the rest smaller and
                 dropped lower, each with its own frame shape. --}}
            @php
                $stripSpan = ['lg:col-span-6', 'lg:col-span-3 lg:mt-24', 'lg:col-span-3 lg:mt-48'];
                $stripShape = ['aspect-[4/3]', 'aspect-[3/4]', 'aspect-square'];
            @endphp
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-6 items-start">
                @foreach ($portfolioProjects as $index => $project)
                    <a href="/portfolio/{{ $project->slug }}" data-tilt class="tilt-card group block {{ $stripSpan[$index % count($stripSpan)] }}">
                        <div class="hud-frame relative p-2 mb-5">
                            <div class="{{ $stripShape[$index % count($stripShape)] }} rounded-lg overflow-hidden bg-space-panel">
                                @if ($project->thumbnail)
                                    <img src="{{ $project->thumbnail }}" alt="{{ $project->name }}" width="600" height="450" loading="lazy" class="w-full h-full object-cover opacity-85 transition-all duration-500 group-hover:scale-105 group-hover:opacity-100">
                                @endif
                            </div>
                        </div>
                        <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-400 mb-2">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }} &middot; {{ $project->category }}</p>
                        <h3 class="font-display font-semibold {{ $index === 0 ? 'text-2xl' : 'text-lg' }} text-white tracking-tight transition-colors group-hover:text-brand-300">{{ $project->name }}</h3>
                    </a>
                @endforeach
            </div>
            <div class="mt-12 flex justify-end">
                <a href="/portfolio" class="btn-space btn-space--glass">Lihat semua portfolio <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
            </div>
        </section>
    @endif

    @if ($pricingPackages->isNotEmpty())
        @php $maxPkgPrice = $pricingPackages->pluck('price')->filter()->max() ?: 1; @endphp
        <section class="max-w-[1440px] mx-auto px-4 md:px-8 py-24 md:py-32">
            <x-public.space-heading index="04" label="Investasi" title="Estimasi Harga" />
            {{-- Price ladder (same idea as the Home teaser): each package is a
                 step with a light-beam bar sized to its price. --}}
            <ol class="space-y-3">
                @foreach ($pricingPackages as $index => $pkg)
                    @php $ratio = $pkg->price ? max(0.15, $pkg->price / $maxPkgPrice) : 0.1; @endphp
                    <li data-reveal data-reveal-delay="{{ $index * 70 }}" style="--step: {{ min($index, 3) }}" class="ladder-step">
                        <div class="group flex flex-col md:flex-row md:items-center gap-3 md:gap-8 rounded-2xl border border-white/10 bg-white/[0.02] px-6 py-4 md:px-8 transition-colors hover:border-brand-400/50 hover:bg-white/[0.04]">
                            <span class="md:w-56 shrink-0">
                                <span class="block font-mono text-[9px] uppercase tracking-[0.25em] text-white/35">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}{{ $pkg->price_prefix ? ' · ' . $pkg->price_prefix : '' }}</span>
                                <span class="block font-display font-semibold text-white tracking-tight">{{ $pkg->name }}</span>
                            </span>
                            <span class="relative h-[3px] rounded-full bg-white/10 flex-1 overflow-hidden" aria-hidden="true">
                                <span class="ladder-beam absolute inset-y-0 left-0 rounded-full" style="width: {{ round($ratio * 100) }}%"></span>
                            </span>
                            <span class="font-display text-xl text-brand-300 tabular-nums md:text-right md:w-44 shrink-0">
                                {{ $pkg->price ? \App\Support\PriceFormatter::idr($pkg->price) : $pkg->price_suffix }}
                            </span>
                        </div>
                    </li>
                @endforeach
            </ol>
            <div class="mt-10 flex justify-end">
                <a href="/harga" class="btn-space btn-space--glass">Lihat detail paket harga <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
            </div>
        </section>
    @endif

    <section class="max-w-[1440px] mx-auto px-4 md:px-8 pb-24 md:pb-32">
        <div data-reveal class="relative overflow-hidden rounded-3xl border border-brand-400/30 bg-space-deep px-6 py-14 md:px-14 md:py-20 text-center">
            <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
            <p class="relative font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400 mb-5">[ 05 ] &mdash; Mulai</p>
            <h2 class="relative font-display font-semibold text-3xl md:text-5xl text-white tracking-tight mb-5 [text-wrap:balance]">Siap Membangun Website Anda?</h2>
            <p class="relative text-white/60 mb-10">Konsultasikan kebutuhan Anda secara gratis, tanpa komitmen.</p>
            <a href="/konsultasi" class="relative btn-space btn-space--lg btn-space--pill">Konsultasi Gratis <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
        </div>
    </section>
</x-layouts.public>
