@php
    $pad = fn ($n) => str_pad($n, 2, '0', STR_PAD_LEFT);
    $domainLabel = function ($url) {
        $host = parse_url($url ?? '', PHP_URL_HOST);
        return $host ? preg_replace('/^www\./', '', $host) : $url;
    };
@endphp

<x-layouts.public
    theme="space"
    seo-title="Portfolio"
    seo-description="Kumpulan proyek website yang sudah kami kerjakan untuk berbagai bisnis dan UMKM."
>
    <x-public.space-page-header
        eyebrow="Portfolio"
        code="WRK"
        title="Hasil Karya Kami"
        subtitle="Beberapa proyek konsep yang menunjukkan pendekatan kami dalam membangun website untuk berbagai kebutuhan bisnis."
    >
        <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-white/45">
            <span class="text-brand-400">{{ $pad($projects->count()) }}</span> proyek terdokumentasi
        </p>
    </x-public.space-page-header>

    <section class="max-w-[1440px] mx-auto px-4 md:px-8 py-16 md:py-24">
        @if ($projects->isEmpty())
            <div class="rounded-2xl border border-dashed border-white/15 text-center py-20 px-6">
                <p class="font-mono text-[11px] uppercase tracking-[0.2em] text-white/45">Belum ada proyek portfolio yang dipublikasikan.</p>
            </div>
        @else
            <div class="space-y-6 md:space-y-8">
                @foreach ($projects as $index => $project)
                    @php $flip = $index % 2 === 1; @endphp
                    <article class="group relative grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center rounded-2xl border border-white/10 bg-white/[0.02] p-4 md:p-6 transition-colors duration-300 hover:border-brand-400/40">
                        <a
                            href="/portfolio/{{ $project->slug }}"
                            data-reveal="{{ $flip ? 'left' : 'right' }}"
                            class="lg:col-span-7 block focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-400 rounded-lg {{ $flip ? 'lg:order-2' : 'lg:order-1' }}"
                            aria-label="{{ $project->name }}"
                        >
                            {{-- Shown as a browser window: reads as "a website we built", not a stock photo --}}
                            <div class="browser-frame">
                                <div class="browser-frame__bar" aria-hidden="true">
                                    <span></span><span></span><span></span>
                                    <span class="browser-frame__url">{{ $project->live_url ? $domainLabel($project->live_url) : 'preview/' . $project->slug }}</span>
                                </div>
                                <div class="aspect-[16/10] overflow-hidden bg-space-panel art-frame">
                                    @if ($project->thumbnail)
                                        <img
                                            src="{{ \App\Support\Img::url($project->thumbnail, 960) }}"
                                            srcset="{{ \App\Support\Img::srcset($project->thumbnail, [480, 960, 1280, 1600]) }}"
                                            sizes="(min-width: 1024px) 55vw, 100vw"
                                            alt="{{ $project->name }}"
                                            width="800"
                                            height="500"
                                            @if ($index > 0) loading="lazy" @else fetchpriority="high" @endif
                                            class="w-full h-full object-cover opacity-85 transition-all duration-700 group-hover:scale-105 group-hover:opacity-100"
                                        >
                                    @else
                                        <div class="w-full h-full flex items-center justify-center font-mono text-[10px] uppercase tracking-[0.25em] text-white/35" aria-hidden="true">
                                            WRK-{{ str_pad($index + 1, 3, '0', STR_PAD_LEFT) }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </a>

                        <div data-reveal="{{ $flip ? 'right' : 'left' }}" data-reveal-delay="80" class="lg:col-span-5 px-2 md:px-4 pb-4 lg:pb-0 {{ $flip ? 'lg:order-1' : 'lg:order-2' }}">
                            <div class="flex flex-wrap items-center gap-3 mb-6">
                                <span class="font-mono text-[10px] uppercase tracking-[0.25em] text-white/45 tabular-nums">
                                    <span class="text-brand-400">{{ $pad($index + 1) }}</span> / {{ $pad($projects->count()) }}
                                </span>
                                @if ($project->category)
                                    <span class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-400">{{ $project->category }}</span>
                                @endif
                                @if ($project->project_type === 'personal')
                                    <span class="font-mono text-[10px] uppercase tracking-[0.18em] text-white/70 border border-white/15 px-2.5 py-1 rounded-full">Contoh Kapabilitas</span>
                                @endif
                            </div>
                            <a href="/portfolio/{{ $project->slug }}" class="block">
                                <h2 class="font-display font-semibold text-3xl md:text-4xl leading-[1.08] tracking-tight text-white mb-4 transition-colors group-hover:text-brand-300 [text-wrap:balance]">{{ $project->name }}</h2>
                            </a>
                            <p class="text-white/55 leading-relaxed mb-8 max-w-md">{{ $project->short_description }}</p>
                            <div class="flex flex-wrap items-center gap-3">
                                <a href="/portfolio/{{ $project->slug }}" class="btn-space btn-space--glass">Lihat studi kasus <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
                                @if ($project->live_url)
                                    <a href="{{ $project->live_url }}" target="_blank" rel="noopener" class="font-mono text-[10px] uppercase tracking-[0.18em] text-white/45 hover:text-brand-300 transition-colors">{{ $domainLabel($project->live_url) }} &nearr;</a>
                                @endif
                            </div>
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
