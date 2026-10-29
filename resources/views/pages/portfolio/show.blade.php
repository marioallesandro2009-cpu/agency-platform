@php
    $pad = fn ($n) => str_pad($n, 2, '0', STR_PAD_LEFT);
    $domainLabel = function ($url) {
        $host = parse_url($url ?? '', PHP_URL_HOST);
        return $host ? preg_replace('/^www\./', '', $host) : $url;
    };

    $breadcrumbItems = [
        ['label' => 'Home', 'href' => '/'],
        ['label' => 'Portfolio', 'href' => '/portfolio'],
        ['label' => $project->name],
    ];

    // "[ 0X ]" numbers for whichever optional case-study blocks this project has.
    $blocks = array_filter([
        'about' => (bool) $project->description,
        'challenge' => (bool) $project->challenge,
        'features' => !empty($project->features),
        'tech' => !empty($project->technologies),
    ]);
    $blockNumbers = [];
    foreach (array_keys($blocks) as $i => $key) {
        $blockNumbers[$key] = $pad($i + 1);
    }

    $cover = $project->cover_image ?: $project->thumbnail;
@endphp

<x-layouts.public
    theme="space"
    :seo-title="$project->seo_title ?: $project->name"
    :seo-description="$project->seo_description ?: $project->short_description"
    :seo-image="$project->thumbnail"
>
    <x-public.space-page-header
        :eyebrow="$project->category ?: 'Portfolio'"
        code="WRK"
        :title="$project->name"
        :subtitle="$project->short_description"
        :breadcrumb-items="$breadcrumbItems"
    >
        <div class="flex flex-wrap items-center gap-3">
            @if ($project->project_type === 'personal')
                <span class="font-mono text-[10px] uppercase tracking-[0.18em] text-white/70 border border-white/15 px-2.5 py-1 rounded-full">Contoh Kapabilitas</span>
            @endif
            @if ($project->client_label)
                <span class="font-mono text-[10px] uppercase tracking-[0.18em] text-white/45">{{ $project->client_label }}</span>
            @endif
        </div>
    </x-public.space-page-header>

    <div class="max-w-[1440px] mx-auto px-4 md:px-8 pt-12 md:pt-16">
        <div data-reveal data-reveal-delay="100" class="browser-frame">
            <div class="browser-frame__bar" aria-hidden="true">
                <span></span><span></span><span></span>
                <span class="browser-frame__url">{{ $project->live_url ? $domainLabel($project->live_url) : 'preview/' . $project->slug }}</span>
            </div>
            <div class="relative h-64 md:h-[32rem] overflow-hidden bg-space-panel">
                @if ($cover)
                    <img
                        src="{{ \App\Support\Img::url($cover, 1280) }}"
                        srcset="{{ \App\Support\Img::srcset($cover, [480, 960, 1280, 1600]) }}"
                        sizes="100vw"
                        alt="{{ $project->name }}"
                        width="1400"
                        height="512"
                        fetchpriority="high"
                        class="w-full h-full object-cover"
                    >
                @endif
            </div>
        </div>
    </div>

    {{-- Spec strip --}}
    <div class="max-w-[1440px] mx-auto px-4 md:px-8 mt-6">
        <dl class="grid grid-cols-2 md:grid-cols-4 gap-px rounded-2xl overflow-hidden border border-white/10 bg-white/10 font-mono text-[10px] uppercase tracking-[0.2em]">
            <div class="bg-space-void px-5 py-4">
                <dt class="text-white/35 mb-1.5">Kategori</dt>
                <dd class="text-white/85">{{ $project->category ?: '—' }}</dd>
            </div>
            <div class="bg-space-void px-5 py-4">
                <dt class="text-white/35 mb-1.5">Tipe</dt>
                <dd class="text-white/85">{{ $project->project_type === 'personal' ? 'Konsep' : 'Klien' }}</dd>
            </div>
            <div class="bg-space-void px-5 py-4">
                <dt class="text-white/35 mb-1.5">Stack</dt>
                <dd class="text-white/85 truncate">{{ !empty($project->technologies) ? implode(' / ', array_slice($project->technologies, 0, 2)) : '—' }}</dd>
            </div>
            <div class="bg-space-void px-5 py-4">
                <dt class="text-white/35 mb-1.5">Live</dt>
                <dd>
                    @if ($project->live_url)
                        <a href="{{ $project->live_url }}" target="_blank" rel="noopener" class="text-brand-400 hover:text-brand-300 truncate block">{{ $domainLabel($project->live_url) }} &nearr;</a>
                    @else
                        <span class="text-white/85">—</span>
                    @endif
                </dd>
            </div>
        </dl>
    </div>

    <section class="max-w-[1440px] mx-auto px-4 md:px-8 py-20 md:py-28 space-y-20 md:space-y-24">
        @isset($blockNumbers['about'])
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-12">
                <p class="lg:col-span-4 font-mono text-[10px] md:text-[11px] uppercase tracking-[0.25em] text-white/45">
                    <span class="text-brand-400">[ {{ $blockNumbers['about'] }} ]</span> Tentang proyek
                </p>
                <p data-reveal class="lg:col-span-8 font-display text-xl md:text-3xl leading-snug text-white/85 tracking-tight [text-wrap:pretty]">{{ $project->description }}</p>
            </div>
        @endisset

        @isset($blockNumbers['challenge'])
            <div class="grid grid-cols-1 md:grid-cols-2 gap-px rounded-2xl overflow-hidden border border-white/10 bg-white/10">
                <div data-reveal class="bg-space-void p-7 md:p-10">
                    <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-white/45 mb-6">
                        <span class="text-brand-400">[ {{ $blockNumbers['challenge'] }} ]</span> Tantangan
                    </p>
                    <p class="text-white/70 leading-relaxed">{{ $project->challenge }}</p>
                </div>
                @if ($project->solution)
                    <div data-reveal data-reveal-delay="80" class="relative bg-space-deep p-7 md:p-10">
                        <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
                        <p class="relative font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400 mb-6">&rarr; Solusi</p>
                        <p class="relative text-white/85 leading-relaxed">{{ $project->solution }}</p>
                    </div>
                @endif
            </div>
        @endisset

        @isset($blockNumbers['features'])
            <div>
                <x-public.space-heading :index="$blockNumbers['features']" label="Fitur" title="Yang Kami Bangun" />
                <ul class="grid grid-cols-1 md:grid-cols-2 gap-px rounded-2xl overflow-hidden border border-white/10 bg-white/10">
                    @foreach ($project->features as $i => $feature)
                        <li data-reveal data-reveal-delay="{{ min($i * 50, 250) }}" class="bg-space-void p-6 flex gap-5 transition-colors hover:bg-space-deep">
                            <span class="font-mono text-[10px] tracking-[0.2em] text-brand-400 pt-1 shrink-0">/{{ $pad($i + 1) }}</span>
                            <span class="text-white/80">{{ $feature }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endisset

        @isset($blockNumbers['tech'])
            <div>
                <x-public.space-heading :index="$blockNumbers['tech']" label="Teknologi" title="Stack yang Dipakai" />
                <div class="flex flex-wrap gap-2.5">
                    @foreach ($project->technologies as $tech)
                        <span class="font-mono text-[11px] uppercase tracking-[0.15em] text-white/80 border border-white/15 bg-white/[0.03] px-4 py-2 rounded-full transition-colors hover:border-brand-400 hover:text-brand-300">{{ $tech }}</span>
                    @endforeach
                </div>
            </div>
        @endisset

        <div class="relative overflow-hidden rounded-3xl border border-brand-400/30 bg-space-deep px-6 py-12 md:px-14 md:py-16 flex flex-col md:flex-row md:items-center md:justify-between gap-8">
            <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
            <div class="relative">
                <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400 mb-4">[ &rarr; ] Langkah berikutnya</p>
                <h2 class="font-display font-semibold text-2xl md:text-4xl text-white tracking-tight mb-2 [text-wrap:balance]">Tertarik dengan hasil serupa?</h2>
                <p class="text-white/55">Konsultasikan kebutuhan website Anda secara gratis.</p>
            </div>
            <div class="relative flex flex-wrap gap-3 shrink-0">
                <a href="/konsultasi" class="btn-space btn-space--lg btn-space--pill">Konsultasi Gratis <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
                @if ($project->live_url)
                    <a href="{{ $project->live_url }}" target="_blank" rel="noopener" class="btn-space btn-space--lg btn-space--glass">Kunjungi situs <span class="btn-space__arrow" aria-hidden="true">&nearr;</span></a>
                @endif
            </div>
        </div>
    </section>

    @if ($related->isNotEmpty())
        <section class="max-w-[1440px] mx-auto px-4 md:px-8 pb-24 md:pb-32">
            <x-public.space-heading label="Lainnya" title="Proyek Terkait" />
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($related as $index => $item)
                    <a href="/portfolio/{{ $item->slug }}" data-reveal data-reveal-delay="{{ $index * 80 }}" class="group block">
                        <div class="aspect-[4/3] rounded-xl overflow-hidden bg-space-panel art-frame ring-1 ring-white/10 mb-5 transition-shadow group-hover:ring-brand-400/40">
                            @if ($item->thumbnail)
                                <img src="{{ \App\Support\Img::url($item->thumbnail, 640) }}" alt="{{ $item->name }}" width="400" height="300" loading="lazy" class="w-full h-full object-cover opacity-85 transition-all duration-500 group-hover:scale-105 group-hover:opacity-100">
                            @endif
                        </div>
                        <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-400 mb-2">{{ $item->category }}</p>
                        <h3 class="font-display font-semibold text-lg text-white tracking-tight transition-colors group-hover:text-brand-300">{{ $item->name }}</h3>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.public>
