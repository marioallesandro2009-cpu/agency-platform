@php
    $pad = fn ($n) => str_pad($n, 2, '0', STR_PAD_LEFT);
    $faqJsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => $faqs->map(fn ($faq) => [
            '@type' => 'Question',
            'name' => $faq->question,
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq->answer],
        ])->values()->all(),
    ];
@endphp

<x-layouts.public
    theme="space"
    seo-title="FAQ"
    seo-description="Pertanyaan yang sering diajukan seputar jasa pembuatan website kami."
    :seo-json-ld="[$faqJsonLd]"
>
    <x-public.space-page-header
        eyebrow="FAQ"
        code="???"
        title="Pertanyaan yang Sering Diajukan"
        subtitle="Temukan jawaban atas pertanyaan umum seputar layanan kami."
    />

    {{-- Search + category filter run client-side (blade-app.js initFaqFilter);
         every answer is in the HTML for SEO and no-JS visitors. --}}
    <section data-faq-filter class="max-w-[1440px] mx-auto px-4 md:px-8 py-16 md:py-24 grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16">
        <aside class="lg:col-span-4">
            <div class="lg:sticky lg:top-28 space-y-8">
                <div>
                    <label for="faq-search" class="block font-mono text-[10px] uppercase tracking-[0.25em] text-white/45 mb-3">Cari pertanyaan</label>
                    <div class="relative">
                        <input
                            id="faq-search"
                            type="search"
                            data-faq-search
                            placeholder="mis. domain, revisi, DP..."
                            class="w-full rounded-full border border-white/15 bg-white/[0.04] pl-5 pr-12 py-3 text-sm text-white placeholder:text-white/35 focus:outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-400/30"
                        >
                        <span data-faq-count class="absolute right-5 top-1/2 -translate-y-1/2 font-mono text-[10px] text-white/35 tabular-nums" aria-live="polite">{{ $pad($faqs->count()) }}</span>
                    </div>
                </div>

                @if ($categories->isNotEmpty())
                    <div>
                        <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-white/45 mb-3">Kategori</p>
                        <div class="flex flex-wrap lg:flex-col lg:items-start gap-2">
                            <button type="button" data-faq-category="" aria-pressed="true" class="space-tab px-4 py-2 rounded-full font-mono text-[11px] uppercase tracking-[0.15em]">Semua</button>
                            @foreach ($categories as $category)
                                <button type="button" data-faq-category="{{ $category }}" aria-pressed="false" class="space-tab px-4 py-2 rounded-full font-mono text-[11px] uppercase tracking-[0.15em]">{{ $category }}</button>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="hidden lg:block relative overflow-hidden rounded-2xl border border-brand-400/30 bg-space-deep p-6">
                    <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
                    <p class="relative font-display font-semibold text-lg text-white mb-2">Masih ada pertanyaan?</p>
                    <p class="relative text-sm text-white/55 mb-5">Tim kami siap membantu kebutuhan spesifik Anda.</p>
                    <a href="/konsultasi" class="relative btn-space btn-space--pill">Konsultasi Gratis <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
                </div>
            </div>
        </aside>

        <div class="lg:col-span-8">
            <div data-faq-empty class="hidden rounded-2xl border border-dashed border-white/15 text-center py-20 px-6">
                <p class="font-mono text-[11px] uppercase tracking-[0.2em] text-white/45">Tidak ada pertanyaan yang cocok.</p>
            </div>

            <div class="border-t border-white/10">
                @foreach ($faqs as $index => $faq)
                    <div
                        data-faq-item
                        data-category="{{ $faq->category }}"
                        data-search="{{ \Illuminate\Support\Str::lower($faq->question . ' ' . $faq->answer) }}"
                        class="faq-row border-b border-white/10"
                    >
                        <button
                            type="button"
                            data-faq-toggle
                            aria-expanded="false"
                            aria-controls="faq-answer-{{ $faq->id }}"
                            class="group w-full flex items-center gap-5 py-6 px-1 md:px-3 text-left"
                        >
                            <span data-faq-num class="faq-row__num font-mono text-[10px] tracking-[0.2em] shrink-0">{{ $pad($index + 1) }}</span>
                            <span class="flex-1">
                                @if ($faq->category)
                                    <span data-faq-cat-label class="block font-mono text-[9px] uppercase tracking-[0.2em] text-white/35 mb-1.5">{{ $faq->category }}</span>
                                @endif
                                <span class="faq-row__q block font-semibold md:text-lg transition-colors group-hover:text-brand-300">{{ $faq->question }}</span>
                            </span>
                            <span data-faq-icon aria-hidden="true" class="faq-row__icon w-9 h-9 shrink-0 rounded-full border flex items-center justify-center transition-all duration-300">+</span>
                        </button>
                        <div id="faq-answer-{{ $faq->id }}" class="hidden pb-7 pl-11 md:pl-14 pr-14 text-white/60 leading-relaxed">{{ $faq->answer }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="lg:hidden max-w-[1440px] mx-auto px-4 md:px-8 pb-24">
        <div data-reveal class="relative overflow-hidden rounded-3xl border border-brand-400/30 bg-space-deep px-6 py-12 text-center">
            <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
            <h2 class="relative font-display font-semibold text-2xl text-white tracking-tight mb-3">Masih ada pertanyaan?</h2>
            <p class="relative text-white/55 mb-8">Tim kami siap membantu menjawab kebutuhan spesifik Anda.</p>
            <a href="/konsultasi" class="relative btn-space btn-space--lg btn-space--pill">Konsultasi Gratis <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
        </div>
    </section>
</x-layouts.public>
