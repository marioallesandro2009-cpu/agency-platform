@php
    $pad = fn ($n) => str_pad($n, 2, '0', STR_PAD_LEFT);
    $domainLabel = function ($url) {
        $host = parse_url($url ?? '', PHP_URL_HOST);
        return $host ? preg_replace('/^www\./', '', $host) : $url;
    };
    // Screenshots keep their own proportions (nothing is cropped); local files are
    // measured so the box is reserved before the image loads. Anything else falls back to 16:10.
    $frameSize = function ($project) {
        $path = $project->thumbnail && str_starts_with($project->thumbnail, '/images/') ? public_path($project->thumbnail) : null;
        $size = $path && is_file($path) ? @getimagesize($path) : false;
        return $size ? [$size[0], $size[1]] : [800, 500];
    };
@endphp

<x-layouts.public
    theme="space"
    seo-title="Portfolio"
    seo-description="Website dan software custom yang sudah kami bangun untuk perusahaan, bisa dilihat langsung."
>
    <x-public.space-page-header
        eyebrow="Portfolio"
        code="WRK"
        title="Hasil Karya Kami"
        subtitle="Beberapa proyek yang bisa Anda lihat langsung. Klik Kunjungi untuk membukanya."
    >
        <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-white/45">
            <span class="text-brand-400">{{ $pad($projects->count()) }}</span> proyek
        </p>
    </x-public.space-page-header>

    <section class="max-w-[1440px] mx-auto px-4 md:px-8 py-16 md:py-24">
        @if ($projects->isEmpty())
            <div class="rounded-2xl border border-dashed border-white/15 text-center py-20 px-6">
                <p class="font-mono text-[11px] uppercase tracking-[0.2em] text-white/45">Belum ada proyek portfolio yang dipublikasikan.</p>
            </div>
        @else
            {{-- Photo and a visit button only: there is deliberately no detail page. --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
                @foreach ($projects as $index => $project)
                    @php [$imgW, $imgH] = $frameSize($project); @endphp
                    <article data-reveal data-reveal-delay="{{ ($index % 2) * 80 }}" class="group flex flex-col rounded-2xl border border-white/10 bg-white/[0.02] p-4 md:p-5 transition-colors duration-300 hover:border-brand-400/40">
                        {{-- Shown as a browser window: reads as "a website we built", not a stock photo --}}
                        <div class="browser-frame">
                            <div class="browser-frame__bar">
                                <span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span>
                                @if ($project->live_url)
                                    <a href="{{ $project->live_url }}" target="_blank" rel="noopener" class="browser-frame__url">{{ $domainLabel($project->live_url) }}</a>
                                @else
                                    <span class="browser-frame__url" aria-hidden="true">preview/{{ $project->slug }}</span>
                                @endif
                            </div>
                            <{{ $project->live_url ? 'a' : 'div' }}
                                @if ($project->live_url)
                                    href="{{ $project->live_url }}" target="_blank" rel="noopener"
                                    aria-label="Kunjungi {{ $project->name }}"
                                @endif
                                class="block focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-brand-400"
                            >
                                <div class="overflow-hidden bg-space-panel {{ $project->project_type === 'personal' ? 'art-frame' : '' }}" style="aspect-ratio: {{ $imgW }} / {{ $imgH }}">
                                    @if ($project->thumbnail)
                                        <img
                                            src="{{ \App\Support\Img::url($project->thumbnail, 960) }}"
                                            srcset="{{ \App\Support\Img::srcset($project->thumbnail, [480, 960, 1280, 1600]) }}"
                                            sizes="(min-width: 768px) 45vw, 100vw"
                                            alt="{{ $project->name }}"
                                            width="{{ $imgW }}"
                                            height="{{ $imgH }}"
                                            @if ($index > 1) loading="lazy" @else fetchpriority="high" @endif
                                            class="w-full h-full object-cover transition-all duration-700 {{ $project->project_type === 'personal' ? 'opacity-85 group-hover:scale-105 group-hover:opacity-100' : 'group-hover:scale-[1.02]' }}"
                                        >
                                    @else
                                        <div class="w-full h-full flex items-center justify-center font-mono text-[10px] uppercase tracking-[0.25em] text-white/35" aria-hidden="true">
                                            WRK-{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}
                                        </div>
                                    @endif
                                </div>
                            </{{ $project->live_url ? 'a' : 'div' }}>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-4 mt-auto pt-6 px-1">
                            <div class="min-w-0">
                                <div class="flex flex-wrap items-center gap-3 mb-2">
                                    @if ($project->category)
                                        <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-400">{{ $project->category }}</span>
                                    @endif
                                    @if ($project->project_type === 'personal')
                                        <span class="font-mono text-[10px] uppercase tracking-[0.18em] text-white/70 border border-white/15 px-2.5 py-1 rounded-full">Contoh Kapabilitas</span>
                                    @endif
                                </div>
                                <h2 class="font-display font-semibold text-2xl leading-tight tracking-tight text-white [text-wrap:balance]">{{ $project->name }}</h2>
                            </div>
                            @if ($project->live_url)
                                <a href="{{ $project->live_url }}" target="_blank" rel="noopener" class="btn-space btn-space--glass shrink-0">Kunjungi <span class="btn-space__arrow" aria-hidden="true">&nearr;</span></a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="max-w-[1440px] mx-auto px-4 md:px-8 pb-24 md:pb-32">
        <div data-reveal class="relative overflow-hidden rounded-3xl border border-brand-400/30 bg-space-deep px-6 py-14 md:px-14 md:py-20 text-center">
            <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
            <p class="relative font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400 mb-5">[ &rarr; ] Proyek berikutnya</p>
            <h2 class="relative font-display font-semibold text-3xl md:text-5xl text-white tracking-tight mb-5 [text-wrap:balance]">Proyek Anda Bisa Jadi yang Berikutnya</h2>
            <p class="relative text-white/60 mb-10">Konsultasikan kebutuhan website Anda secara gratis, tanpa komitmen.</p>
            <a href="/konsultasi" class="relative btn-space btn-space--lg btn-space--pill">Konsultasi Gratis <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
        </div>
    </section>
</x-layouts.public>
