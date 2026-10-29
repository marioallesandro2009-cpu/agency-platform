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
                    <span class="col-span-2 md:col-span-1 font-display font-extrabold text-3xl md:text-5xl leading-none tabular-nums text-transparent [-webkit-text-stroke:1px_rgba(255,255,255,0.25)] transition-all duration-500 group-hover:[-webkit-text-stroke:1px_rgba(163,230,53,0.9)]" aria-hidden="true">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
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
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-5">
                @foreach ($whyWebsite as $i => $item)
                    <div data-reveal data-reveal-delay="{{ $i * 80 }}" class="rounded-2xl border border-white/10 bg-space-void/70 backdrop-blur-md p-7 md:p-8">
                        <span class="block font-mono text-[10px] tracking-[0.25em] text-brand-400 mb-10">/{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="font-display font-semibold text-xl text-white tracking-tight mb-3">{{ $item['title'] }}</h3>
                        <p class="text-sm text-white/55 leading-relaxed">{{ $item['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if ($portfolioProjects->isNotEmpty())
        <section class="max-w-[1440px] mx-auto px-4 md:px-8 py-24 md:py-32">
            <x-public.space-heading index="03" label="Portfolio" title="Contoh Hasil Kerja Kami" />
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($portfolioProjects as $index => $project)
                    <a href="/portfolio/{{ $project->slug }}" data-reveal data-reveal-delay="{{ $index * 80 }}" class="group block">
                        <div class="hud-frame relative p-2 mb-5">
                            <div class="aspect-[4/3] rounded-lg overflow-hidden bg-space-panel">
                                @if ($project->thumbnail)
                                    <img src="{{ $project->thumbnail }}" alt="{{ $project->name }}" width="400" height="300" loading="lazy" class="w-full h-full object-cover opacity-85 transition-all duration-500 group-hover:scale-105 group-hover:opacity-100">
                                @endif
                            </div>
                        </div>
                        <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-400 mb-2">{{ $project->category }}</p>
                        <h3 class="font-display font-semibold text-lg text-white tracking-tight transition-colors group-hover:text-brand-300">{{ $project->name }}</h3>
                    </a>
                @endforeach
            </div>
            <div class="mt-12 flex justify-end">
                <a href="/portfolio" class="btn-space btn-space--glass">Lihat semua portfolio <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
            </div>
        </section>
    @endif

    @if ($pricingPackages->isNotEmpty())
        <section class="max-w-[1440px] mx-auto px-4 md:px-8 py-24 md:py-32">
            <x-public.space-heading index="04" label="Investasi" title="Estimasi Harga" />
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-px rounded-2xl overflow-hidden border border-white/10 bg-white/10">
                @foreach ($pricingPackages as $index => $pkg)
                    <div data-reveal data-reveal-delay="{{ $index * 60 }}" class="bg-space-void p-6 flex flex-col min-h-[180px] transition-colors hover:bg-space-deep">
                        <span class="font-mono text-[10px] tracking-[0.2em] text-white/30 mb-auto">/{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="font-display font-semibold text-white tracking-tight mt-8 mb-3">{{ $pkg->name }}</h3>
                        @if ($pkg->price_prefix)
                            <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-white/40">{{ $pkg->price_prefix }}</p>
                        @endif
                        @if ($pkg->price)
                            <p class="font-display text-xl text-brand-300 tabular-nums">{{ \App\Support\PriceFormatter::idr($pkg->price) }}</p>
                        @elseif ($pkg->price_suffix)
                            <p class="font-display text-xl text-brand-300">{{ $pkg->price_suffix }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
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
