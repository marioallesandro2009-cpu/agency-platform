@php
    $breadcrumbItems = [
        ['label' => 'Home', 'href' => '/'],
        ['label' => 'Blog', 'href' => '/blog'],
        ['label' => $post->title],
    ];
    $readingTime = \App\Support\ReadingTime::minutes($post->content);
    $formatDate = fn ($date) => $date ? \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('d F Y') : '';
@endphp

<x-layouts.public
    theme="space"
    :seo-title="$post->seo_title ?: $post->title"
    :seo-description="$post->seo_description ?: $post->excerpt"
    :seo-image="$post->featured_image"
    seo-type="article"
>
    <x-public.space-page-header
        :eyebrow="$post->category?->name ?? 'Blog'"
        code="LOG"
        :title="$post->title"
        :subtitle="$post->excerpt"
        :breadcrumb-items="$breadcrumbItems"
    >
        <div class="flex flex-wrap items-center gap-x-5 gap-y-2 font-mono text-[10px] uppercase tracking-[0.2em] text-white/50">
            @if ($post->author)
                <span class="text-white/80">{{ $post->author->name }}</span>
            @endif
            @if ($post->published_at)
                <span>{{ $formatDate($post->published_at) }}</span>
            @endif
            <span class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-brand-400" aria-hidden="true"></span>{{ $readingTime }} menit baca</span>
        </div>
    </x-public.space-page-header>

    <div class="max-w-[1440px] mx-auto px-4 md:px-8 py-16 md:py-24 grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12">
        {{-- Left rail: article meta, sticks while reading --}}
        <aside class="hidden lg:block lg:col-span-3">
            <div class="sticky top-28 space-y-6 font-mono text-[10px] uppercase tracking-[0.2em]">
                <a href="/blog" class="group inline-flex items-center gap-2 text-white/50 hover:text-white transition-colors">
                    <span aria-hidden="true" class="transition-transform group-hover:-translate-x-1">&larr;</span> Semua artikel
                </a>
                <dl class="border-t border-white/10">
                    @if ($post->category)
                        <div class="flex justify-between gap-4 py-3 border-b border-white/10">
                            <dt class="text-white/35">Kategori</dt>
                            <dd><a href="/blog/category/{{ $post->category->slug }}" class="text-brand-400 hover:text-brand-300">{{ $post->category->name }}</a></dd>
                        </div>
                    @endif
                    @if ($post->author)
                        <div class="flex justify-between gap-4 py-3 border-b border-white/10">
                            <dt class="text-white/35">Penulis</dt>
                            <dd class="text-white/75 text-right">{{ $post->author->name }}</dd>
                        </div>
                    @endif
                    @if ($post->published_at)
                        <div class="flex justify-between gap-4 py-3 border-b border-white/10">
                            <dt class="text-white/35">Terbit</dt>
                            <dd class="text-white/75 text-right">{{ $formatDate($post->published_at) }}</dd>
                        </div>
                    @endif
                    <div class="flex justify-between gap-4 py-3 border-b border-white/10">
                        <dt class="text-white/35">Durasi</dt>
                        <dd class="text-white/75">{{ $readingTime }} menit</dd>
                    </div>
                </dl>
            </div>
        </aside>

        <article class="lg:col-span-8 min-w-0">
            @if ($post->featured_image)
                <div data-reveal data-reveal-delay="100" class="hud-frame relative p-2 mb-12">
                    <img src="{{ $post->featured_image }}" alt="{{ $post->featured_image_alt ?: $post->title }}" width="1200" height="675" class="w-full rounded-lg object-cover max-h-[28rem]">
                </div>
            @endif

            <div class="prose prose-invert prose-lg max-w-3xl text-white/75 whitespace-pre-line prose-headings:font-display prose-headings:font-semibold prose-headings:tracking-tight prose-headings:text-white prose-a:text-brand-300 prose-strong:text-white prose-blockquote:border-brand-400 prose-blockquote:text-white/80 prose-li:marker:text-brand-400 prose-img:rounded-xl prose-hr:border-white/10 prose-code:text-brand-200">{!! $post->content !!}</div>

            <div class="mt-16 max-w-3xl relative overflow-hidden rounded-2xl border border-brand-400/30 bg-space-deep p-7 md:p-9">
                <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
                <p class="relative font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400 mb-4">[ &rarr; ] Langkah berikutnya</p>
                <p class="relative font-display font-semibold text-2xl md:text-3xl text-white tracking-tight mb-6 [text-wrap:balance]">Mau website seperti yang dibahas di artikel ini?</p>
                <a href="/konsultasi" class="relative btn-space btn-space--lg btn-space--pill">Konsultasi Gratis Sekarang <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
            </div>
        </article>
    </div>

    @if ($related->isNotEmpty())
        <section class="max-w-[1440px] mx-auto px-4 md:px-8 pb-24 md:pb-32">
            <x-public.space-heading index="01" label="Baca juga" title="Artikel Terkait" />
            <div class="grid grid-cols-1 md:grid-cols-3 gap-px rounded-2xl overflow-hidden border border-white/10 bg-white/10">
                @foreach ($related as $i => $r)
                    <a href="/blog/{{ $r->slug }}" data-reveal data-reveal-delay="{{ $i * 80 }}" class="group bg-space-void p-7 flex flex-col min-h-[200px] transition-colors hover:bg-space-deep">
                        <span class="font-mono text-[10px] tracking-[0.2em] text-white/30 mb-auto">/{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="font-display font-semibold text-lg text-white tracking-tight mt-8 mb-3 line-clamp-2 transition-colors group-hover:text-brand-300">{{ $r->title }}</h3>
                        <p class="flex items-center justify-between font-mono text-[10px] uppercase tracking-[0.18em] text-white/40">
                            {{ $formatDate($r->published_at) }}
                            <span aria-hidden="true" class="text-brand-400 transition-transform duration-300 group-hover:translate-x-1">&rarr;</span>
                        </p>
                    </a>
                @endforeach
            </div>
        </section>
    @endif
</x-layouts.public>
