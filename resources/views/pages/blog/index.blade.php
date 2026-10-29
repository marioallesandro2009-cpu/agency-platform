@php
    $seoTitle = $activeCategory ? 'Kategori: ' . $activeCategory->name : 'Blog & Artikel';
    $seoDescription = $activeCategory ? "Kumpulan artikel seputar {$activeCategory->name}." : 'Artikel dan insight seputar pembuatan website, UMKM, dan bisnis online.';
    $pageTitle = $activeCategory ? 'Kategori: ' . $activeCategory->name : 'Artikel & Insight';

    $formatDate = fn ($date) => $date ? \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('d F Y') : '';

    // Newest post gets the wide "featured" slot; the rest go in the grid.
    $featured = $posts->first();
    $rest = $posts->slice(1);
@endphp

<x-layouts.public theme="space" :seo-title="$seoTitle" :seo-description="$seoDescription">
    <x-public.space-page-header
        eyebrow="Blog"
        code="LOG"
        :title="$pageTitle"
        subtitle="Artikel dan insight seputar pembuatan website, UMKM, dan bisnis online."
    />

    <section class="max-w-[1440px] mx-auto px-4 md:px-8 py-16 md:py-24">
        <nav class="flex flex-wrap items-center gap-2 mb-14" aria-label="Kategori artikel">
            <span class="hidden sm:inline font-mono text-[10px] uppercase tracking-[0.25em] text-white/40 mr-3">Filter &rarr;</span>
            <a href="/blog" @if (!$activeCategory) aria-current="page" @endif class="space-tab px-5 py-2.5 rounded-full font-mono text-[11px] uppercase tracking-[0.15em]">
                Semua
            </a>
            @foreach ($categories as $cat)
                @php $isActive = $activeCategory?->id === $cat->id; @endphp
                <a href="/blog/category/{{ $cat->slug }}" @if ($isActive) aria-current="page" @endif class="space-tab px-5 py-2.5 rounded-full font-mono text-[11px] uppercase tracking-[0.15em]">
                    {{ $cat->name }}
                </a>
            @endforeach
        </nav>

        @if ($posts->isEmpty())
            <div class="rounded-2xl border border-dashed border-white/15 text-center py-20 px-6">
                @if ($mascots['empty_state_blog']['image_path'] ?? null)
                    <img src="{{ $mascots['empty_state_blog']['image_path'] }}" alt="{{ $mascots['empty_state_blog']['alt_text'] ?? '' }}" width="128" height="128" loading="lazy" class="w-32 h-32 mx-auto mb-6 object-contain">
                @endif
                <p class="font-mono text-[11px] uppercase tracking-[0.2em] text-white/45">Belum ada artikel di kategori ini.</p>
            </div>
        @endif

        @if ($featured)
            {{-- Featured: newest post, wide card --}}
            <article data-reveal class="group mb-16 md:mb-20">
                <a href="/blog/{{ $featured->slug }}" class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center rounded-2xl border border-white/10 bg-white/[0.02] p-4 md:p-6 transition-colors hover:border-brand-400/40">
                    <div class="lg:col-span-7">
                        <div class="hud-frame relative p-2">
                            <div class="aspect-[16/9] rounded-lg overflow-hidden bg-space-panel">
                                @if ($featured->featured_image)
                                    <img src="{{ $featured->featured_image }}" alt="{{ $featured->featured_image_alt ?: $featured->title }}" width="800" height="450" class="w-full h-full object-cover opacity-90 transition-all duration-700 group-hover:scale-105 group-hover:opacity-100">
                                @else
                                    <div class="w-full h-full bg-grid-subtle flex items-center justify-center font-mono text-[10px] uppercase tracking-[0.25em] text-white/35" aria-hidden="true">LOG-001</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="lg:col-span-5 lg:pr-6">
                        <div class="flex flex-wrap items-center gap-3 font-mono text-[10px] uppercase tracking-[0.2em] text-white/45 mb-5">
                            <span class="bg-brand-400 text-space-void px-2.5 py-1 rounded-full">Terbaru</span>
                            @if ($featured->category)
                                <span class="text-brand-400">{{ $featured->category->name }}</span>
                            @endif
                        </div>
                        <h2 class="font-display font-semibold text-2xl md:text-4xl leading-[1.1] tracking-tight text-white mb-4 transition-colors group-hover:text-brand-300 [text-wrap:balance]">{{ $featured->title }}</h2>
                        <p class="text-white/55 leading-relaxed mb-6 line-clamp-3">{{ $featured->excerpt }}</p>
                        <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-white/40">
                            {{ $formatDate($featured->published_at) }} &middot; {{ \App\Support\ReadingTime::minutes($featured->content) }} menit baca
                        </p>
                    </div>
                </a>
            </article>
        @endif

        @if ($rest->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-6 gap-y-12">
                @foreach ($rest->values() as $index => $post)
                    <article data-reveal data-reveal-delay="{{ min($index, 5) * 80 }}" class="group">
                        <a href="/blog/{{ $post->slug }}" class="block">
                            <div class="aspect-[5/3] rounded-xl overflow-hidden bg-space-panel ring-1 ring-white/10 mb-5 transition-shadow group-hover:ring-brand-400/40">
                                @if ($post->featured_image)
                                    <img src="{{ $post->featured_image }}" alt="{{ $post->featured_image_alt ?: $post->title }}" width="400" height="240" loading="lazy" class="w-full h-full object-cover opacity-85 transition-all duration-500 group-hover:scale-105 group-hover:opacity-100">
                                @else
                                    <div class="w-full h-full bg-grid-subtle flex items-center justify-center font-mono text-[10px] uppercase tracking-[0.25em] text-white/35" aria-hidden="true">LOG-{{ str_pad($index + 2, 3, '0', STR_PAD_LEFT) }}</div>
                                @endif
                            </div>
                            <div class="flex flex-wrap items-center gap-3 font-mono text-[10px] uppercase tracking-[0.18em] text-white/40 mb-3">
                                @if ($post->category)
                                    <span class="text-brand-400">{{ $post->category->name }}</span>
                                @endif
                                <span>{{ $formatDate($post->published_at) }}</span>
                            </div>
                            <h2 class="font-display font-semibold text-lg tracking-tight text-white mb-2 line-clamp-2 transition-colors group-hover:text-brand-300">{{ $post->title }}</h2>
                            <p class="text-sm text-white/50 mb-4 line-clamp-2 leading-relaxed">{{ $post->excerpt }}</p>
                            <p class="flex items-center gap-2 font-mono text-[10px] uppercase tracking-[0.18em] text-white/40 group-hover:text-white transition-colors">
                                {{ \App\Support\ReadingTime::minutes($post->content) }} menit baca
                                <span aria-hidden="true" class="transition-transform duration-300 group-hover:translate-x-1">&rarr;</span>
                            </p>
                        </a>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.public>
