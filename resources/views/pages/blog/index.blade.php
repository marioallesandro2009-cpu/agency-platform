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
                                    <img src="{{ \App\Support\Img::url($featured->featured_image, 960) }}" srcset="{{ \App\Support\Img::srcset($featured->featured_image, [480, 960, 1280, 1600]) }}" sizes="(min-width: 1024px) 55vw, 100vw" alt="{{ $featured->featured_image_alt ?: $featured->title }}" width="800" height="450" class="w-full h-full object-cover opacity-90 transition-all duration-700 group-hover:scale-105 group-hover:opacity-100">
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
            {{--
                Orbit timeline instead of an even grid: posts alternate left
                and right of a dashed center trajectory, each with its own
                image shape and vertical offset, hung off a numbered node.
            --}}
            @php $shapes = ['aspect-[16/10]', 'aspect-[4/5]', 'aspect-[21/10]', 'aspect-square']; @endphp
            <div class="relative">
                <span class="hidden lg:block absolute left-1/2 top-0 bottom-0 -translate-x-1/2 border-l border-dashed border-white/15" aria-hidden="true"></span>
                <div class="space-y-16 lg:space-y-0">
                    @foreach ($rest->values() as $index => $post)
                        @php $right = $index % 2 === 1; @endphp
                        <article data-reveal="{{ $right ? 'right' : 'left' }}" class="group relative lg:w-[44%] {{ $right ? 'lg:ml-auto lg:-mt-24' : ($index > 0 ? 'lg:-mt-24' : '') }}">
                            <span class="hidden lg:flex absolute top-10 {{ $right ? '-left-[calc(13.6%_+_22px)]' : '-right-[calc(13.6%_+_22px)]' }} w-11 h-11 rounded-full border border-white/15 bg-space-void items-center justify-center font-mono text-[10px] text-white/50 transition-all duration-300 group-hover:bg-brand-400 group-hover:border-brand-400 group-hover:text-space-void group-hover:shadow-[0_0_30px_-4px_rgb(var(--lime-400)/0.8)]" aria-hidden="true">{{ str_pad($index + 2, 2, '0', STR_PAD_LEFT) }}</span>
                            <a href="/blog/{{ $post->slug }}" data-tilt class="tilt-card block">
                                <div class="relative {{ $shapes[$index % count($shapes)] }} rounded-2xl overflow-hidden bg-space-panel ring-1 ring-white/10 mb-6 transition-shadow group-hover:ring-brand-400/40">
                                    @if ($post->featured_image)
                                        <img src="{{ \App\Support\Img::url($post->featured_image, 960) }}" srcset="{{ \App\Support\Img::srcset($post->featured_image, [480, 960, 1280, 1600]) }}" sizes="(min-width: 1024px) 40vw, 100vw" alt="{{ $post->featured_image_alt ?: $post->title }}" width="560" height="420" loading="lazy" class="w-full h-full object-cover opacity-85 transition-all duration-700 group-hover:scale-105 group-hover:opacity-100">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center font-mono text-[10px] uppercase tracking-[0.25em] text-white/35 bg-[repeating-linear-gradient(135deg,transparent_0_14px,rgb(255_255_255/0.04)_14px_15px)]" aria-hidden="true">LOG-{{ str_pad($index + 2, 3, '0', STR_PAD_LEFT) }}</div>
                                    @endif
                                    <span class="absolute bottom-4 {{ $right ? 'left-4' : 'right-4' }} font-mono text-[10px] uppercase tracking-[0.18em] text-white bg-black/40 backdrop-blur-md border border-white/15 px-3 py-1 rounded-full">{{ \App\Support\ReadingTime::minutes($post->content) }} menit</span>
                                </div>
                                <div class="{{ $right ? 'lg:pl-10' : 'lg:pr-10' }}">
                                    <div class="flex flex-wrap items-center gap-3 font-mono text-[10px] uppercase tracking-[0.18em] text-white/40 mb-3">
                                        @if ($post->category)
                                            <span class="text-brand-400">{{ $post->category->name }}</span>
                                        @endif
                                        <span>{{ $formatDate($post->published_at) }}</span>
                                    </div>
                                    <h2 class="font-display font-semibold text-xl md:text-2xl leading-snug tracking-tight text-white mb-3 transition-colors group-hover:text-brand-300 [text-wrap:balance]">{{ $post->title }}</h2>
                                    <p class="text-sm text-white/50 line-clamp-2 leading-relaxed">{{ $post->excerpt }}</p>
                                </div>
                            </a>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif
    </section>
</x-layouts.public>
