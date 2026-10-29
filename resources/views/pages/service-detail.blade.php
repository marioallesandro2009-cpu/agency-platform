@php
    $waNumber = $settings['contact']['whatsapp_number'] ?? null;
    $sidebarMascotKeys = [
        'jasa-website-umkm' => 'sidebar_jasa_umkm',
        'jasa-website-company-profile' => 'sidebar_jasa_company_profile',
        'jasa-toko-online' => 'sidebar_jasa_toko_online',
        'jasa-web-development' => 'sidebar_jasa_web_development',
    ];
    $sidebarMascot = $mascots[$sidebarMascotKeys[$service->slug] ?? ''] ?? [];

    $serviceJsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $service->name,
        'description' => $service->seo_description ?: $service->short_description,
        'provider' => [
            '@type' => 'Organization',
            'name' => $settings['general']['website_name'] ?? null,
        ],
    ];

    $breadcrumbItems = [
        ['label' => 'Home', 'href' => '/'],
        ['label' => 'Jasa & Layanan', 'href' => '/jasa-website'],
        ['label' => $service->name],
    ];

    // "[ 0X ]" numbers for whichever optional blocks this service has, computed
    // up front (component attributes can be evaluated more than once, so a
    // running counter inside the template would skip numbers).
    $blockNumbers = [];
    $optionalBlocks = [
        'audience' => (bool) $service->target_audience,
        'features' => !empty($service->features),
        'benefits' => !empty($service->benefits),
        'process' => !empty($service->process_steps),
        'portfolio' => $relatedPortfolio->isNotEmpty(),
        'faq' => !empty($service->faqs),
    ];
    foreach (array_keys(array_filter($optionalBlocks)) as $i => $key) {
        $blockNumbers[$key] = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
    }
@endphp

<x-layouts.public
    theme="space"
    :seo-title="$service->seo_title ?: $service->name"
    :seo-description="$service->seo_description ?: $service->short_description"
    :seo-json-ld="[$serviceJsonLd]"
>
    <x-public.space-page-header
        eyebrow="Jasa & Layanan"
        code="SVC"
        :title="$service->name"
        :subtitle="$service->short_description"
        :breadcrumb-items="$breadcrumbItems"
    >
        <div class="flex flex-wrap gap-3">
            <a href="{{ $service->cta_url ?: '/konsultasi' }}" class="btn-space btn-space--lg btn-space--pill">{{ $service->cta_text ?: 'Konsultasi Gratis' }} <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
            <a href="/harga" class="btn-space btn-space--lg btn-space--glass">Lihat Harga <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
        </div>
    </x-public.space-page-header>

    <div class="max-w-[1440px] mx-auto px-4 md:px-8 py-20 md:py-28 grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-16">
        <div class="lg:col-span-8 min-w-0">
            @if ($service->image)
                <div data-reveal class="hud-frame relative p-2 mb-14">
                    <div class="aspect-[16/8] rounded-lg overflow-hidden bg-space-panel art-frame">
                        <img src="{{ \App\Support\Img::url($service->image, 1280) }}" srcset="{{ \App\Support\Img::srcset($service->image, [480, 960, 1280, 1600]) }}" sizes="(min-width: 1024px) 60vw, 100vw" alt="{{ $service->name }}" width="960" height="480" class="w-full h-full object-cover">
                    </div>
                </div>
            @endif

            <div data-reveal class="prose prose-invert prose-lg max-w-none text-white/70 leading-relaxed whitespace-pre-line prose-a:text-brand-300 prose-strong:text-white prose-headings:font-display prose-headings:tracking-tight">{!! $service->long_description ?: $service->description !!}</div>

            @if ($service->target_audience)
                <div data-reveal class="mt-14 relative overflow-hidden rounded-2xl border border-brand-400/30 bg-brand-400/[0.05] p-7 md:p-8">
                    <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400 mb-4">[ {{ $blockNumbers['audience'] }} ] &mdash; Untuk siapa layanan ini?</p>
                    <p class="font-display text-lg md:text-xl text-white leading-snug">{{ $service->target_audience }}</p>
                </div>
            @endif

            @if (!empty($service->features))
                <div class="mt-20">
                    <x-public.space-heading :index="$blockNumbers['features']" label="Fitur" title="Yang Anda Dapatkan" />
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-px rounded-2xl overflow-hidden border border-white/10 bg-white/10">
                        @foreach ($service->features as $i => $f)
                            <li data-reveal data-reveal-delay="{{ min($i * 50, 250) }}" class="bg-space-void p-6 flex gap-5 transition-colors hover:bg-space-deep">
                                <span class="font-mono text-[10px] tracking-[0.2em] text-brand-400 pt-1 shrink-0">/{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                <span class="text-white/80">{{ $f }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (!empty($service->benefits))
                <div class="mt-20">
                    <x-public.space-heading :index="$blockNumbers['benefits']" label="Manfaat" title="Dampaknya untuk Bisnis Anda" />
                    <ul class="space-y-px rounded-2xl overflow-hidden border border-white/10 bg-white/10">
                        @foreach ($service->benefits as $i => $b)
                            <li data-reveal data-reveal-delay="{{ min($i * 50, 250) }}" class="bg-space-void px-6 py-5 flex items-start gap-4">
                                <span class="text-brand-400 leading-none mt-1 shrink-0" aria-hidden="true">&#10022;</span>
                                <span class="text-white/80">{{ $b }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (!empty($service->process_steps))
                <div class="mt-20">
                    <x-public.space-heading :index="$blockNumbers['process']" label="Proses" title="Proses Pengerjaan" />
                    <ol class="relative grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($service->process_steps as $index => $step)
                            <li data-reveal data-reveal-delay="{{ min($index * 80, 320) }}" class="relative rounded-2xl border border-white/10 bg-white/[0.03] p-6">
                                <div class="flex items-center gap-3 mb-6">
                                    <span class="process-node w-10 h-10 rounded-full border border-white/15 bg-space-deep flex items-center justify-center font-mono text-xs text-brand-400">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="h-px flex-1 border-t border-dashed border-white/15" aria-hidden="true"></span>
                                </div>
                                <p class="text-white/85">{{ $step }}</p>
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endif

            <div class="mt-20 rounded-2xl border border-white/10 bg-white/[0.03] p-7 md:p-8 flex flex-wrap items-center justify-between gap-8">
                <div>
                    <p class="font-display text-xl md:text-2xl text-white tracking-tight mb-5 max-w-md">Siap mulai {{ $service->name }}?</p>
                    <a href="{{ $service->cta_url ?: '/konsultasi' }}" class="btn-space btn-space--lg btn-space--pill">
                        {{ $service->cta_text ?: 'Konsultasi Gratis Sekarang' }} <span class="btn-space__arrow" aria-hidden="true">&rarr;</span>
                    </a>
                    @if (!empty($sidebarMascot['caption']))
                        <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-white/40 mt-4">{{ $sidebarMascot['caption'] }}</p>
                    @endif
                </div>
                @if (!empty($sidebarMascot['image_path']))
                    <div class="relative shrink-0 w-28 h-28 sm:w-36 sm:h-36 lg:w-44 lg:h-44 ambient-float">
                        <div class="absolute inset-2 -z-10 rounded-full bg-brand-400/10" aria-hidden="true"></div>
                        <img src="{{ AppSupportImg::url($sidebarMascot['image_path'], 480) }}" alt="{{ $sidebarMascot['alt_text'] ?? '' }}" width="220" height="220" loading="lazy" class="w-full h-full object-contain">
                    </div>
                @endif
            </div>

            @if ($relatedPortfolio->isNotEmpty())
                <div class="mt-20">
                    <x-public.space-heading :index="$blockNumbers['portfolio']" label="Portfolio" title="Portfolio Terkait" />
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach ($relatedPortfolio as $project)
                            <a href="/portfolio/{{ $project->slug }}" class="group block rounded-2xl border border-white/10 bg-white/[0.03] overflow-hidden transition-colors hover:border-brand-400/40">
                                <div class="aspect-[4/3] bg-space-panel overflow-hidden">
                                    @if ($project->thumbnail)
                                        <img src="{{ \App\Support\Img::url($project->thumbnail, 480) }}" alt="{{ $project->name }}" width="320" height="240" loading="lazy" class="w-full h-full object-cover opacity-85 transition-all duration-500 group-hover:scale-105 group-hover:opacity-100">
                                    @endif
                                </div>
                                <div class="p-5">
                                    <p class="font-display font-semibold text-white group-hover:text-brand-300 transition-colors line-clamp-1">{{ $project->name }}</p>
                                    @if ($project->short_description)
                                        <p class="text-sm text-white/50 mt-1 line-clamp-2">{{ $project->short_description }}</p>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if (!empty($service->faqs))
                <div class="mt-20">
                    <x-public.space-heading :index="$blockNumbers['faq']" label="FAQ" title="Pertanyaan Umum" />
                    <div class="border-t border-white/10">
                        @foreach ($service->faqs as $index => $faq)
                            <div class="border-b border-white/10">
                                <button type="button" data-faq-toggle aria-expanded="false" aria-controls="service-faq-answer-{{ $index }}" class="group w-full flex items-center gap-5 py-6 text-left">
                                    <span class="font-mono text-[10px] tracking-[0.2em] text-white/55 shrink-0">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="flex-1 font-semibold text-white/85 transition-colors group-hover:text-brand-300">{{ $faq['question'] }}</span>
                                    <span data-faq-icon aria-hidden="true" class="w-8 h-8 shrink-0 rounded-full border border-white/15 flex items-center justify-center text-brand-400 transition-transform duration-300">+</span>
                                </button>
                                <div id="service-faq-answer-{{ $index }}" class="hidden pb-6 pl-10 pr-12 text-white/55 leading-relaxed">{{ $faq['answer'] }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <aside class="lg:col-span-4">
            <div class="lg:sticky lg:top-28 space-y-5">
                <div data-reveal="right" class="relative overflow-hidden rounded-2xl border border-brand-400/30 bg-space-deep p-7">
                    <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
                    <p class="relative font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400 mb-4">Butuh bantuan?</p>
                    <h3 class="relative font-display font-semibold text-xl text-white tracking-tight mb-3">Bingung Menentukan Paket?</h3>
                    <p class="relative text-sm text-white/55 mb-6 leading-relaxed">Konsultasikan kebutuhan Anda langsung, gratis tanpa komitmen.</p>
                    <div class="relative flex flex-col gap-2.5">
                        @if ($waNumber)
                            <a href="https://wa.me/{{ $waNumber }}?text={{ rawurlencode('Halo, saya mau konsultasi soal ' . $service->name) }}" target="_blank" rel="noopener" class="btn-space btn-space--pill justify-between w-full">
                                Chat WhatsApp <span class="btn-space__arrow" aria-hidden="true">&rarr;</span>
                            </a>
                        @endif
                        <a href="{{ $service->cta_url ?: '/konsultasi' }}" class="btn-space btn-space--glass justify-between w-full">
                            {{ $service->cta_text ?: 'Konsultasi Gratis' }} <span class="btn-space__arrow" aria-hidden="true">&rarr;</span>
                        </a>
                    </div>
                </div>

                @if ($otherServices->isNotEmpty())
                    <div class="rounded-2xl border border-white/10 p-6">
                        <h3 class="font-mono text-[10px] uppercase tracking-[0.25em] text-white/45 mb-5">Layanan lainnya</h3>
                        <div class="space-y-1">
                            @foreach ($otherServices as $i => $s)
                                <a href="/{{ $s->slug }}" class="group flex items-center gap-4 rounded-xl px-3 py-3 -mx-3 transition-colors hover:bg-white/5">
                                    <span class="font-mono text-[10px] tracking-[0.2em] text-white/30">/{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                                    <span class="text-sm text-white/80 group-hover:text-white transition-colors flex-1 min-w-0 line-clamp-2">{{ $s->name }}</span>
                                    <span aria-hidden="true" class="text-brand-400 opacity-0 -translate-x-1 transition-all duration-200 group-hover:opacity-100 group-hover:translate-x-0">&rarr;</span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if ($relatedPosts->isNotEmpty())
                    <div class="rounded-2xl border border-white/10 p-6">
                        <h3 class="font-mono text-[10px] uppercase tracking-[0.25em] text-white/45 mb-5">Artikel terkait</h3>
                        <div class="space-y-5">
                            @foreach ($relatedPosts as $post)
                                <a href="/blog/{{ $post->slug }}" class="block group">
                                    <p class="text-sm text-white/80 group-hover:text-brand-300 transition-colors line-clamp-2">{{ $post->title }}</p>
                                    <p class="font-mono text-[10px] uppercase tracking-[0.15em] text-white/35 mt-1.5">{{ $post->published_at ? \Carbon\Carbon::parse($post->published_at)->locale('id')->translatedFormat('d F Y') : '' }}</p>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </aside>
    </div>

    <section class="contact-cta relative pb-24 md:pb-32 overflow-hidden">
        <div class="max-w-[1440px] mx-auto px-4 md:px-8 relative">
            <div class="relative overflow-hidden rounded-3xl border border-white/10 bg-space-deep/70 px-6 py-12 md:px-14 md:py-16">
                <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
                <div class="relative grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-start">
                    <div class="lg:col-span-5">
                        <p data-reveal class="flex items-center gap-3 font-mono text-[10px] md:text-[11px] uppercase tracking-[0.25em] text-white/50 mb-5">
                            <span class="text-brand-400">[ &rarr; ]</span>
                            Mulai sekarang
                        </p>
                        <h2 data-reveal data-reveal-delay="80" class="font-display font-semibold text-3xl md:text-5xl leading-[1.05] tracking-tight text-white mb-5 [text-wrap:balance]">
                            Siap Membangun {{ $service->name }} Anda?
                        </h2>
                        <p data-reveal data-reveal-delay="120" class="text-white/60">Ceritakan kebutuhan Anda, tim kami akan bantu wujudkan dalam waktu singkat.</p>
                    </div>

                    <div class="lg:col-span-7">
                        <div id="service-cta-form-success" class="hidden flex items-start gap-3 bg-white/5 border border-white/10 rounded-lg p-5">
                            <span class="text-brand-400 text-xl leading-none shrink-0 mt-0.5" aria-hidden="true">&#10022;</span>
                            <div>
                                <p class="font-semibold text-white mb-1">Terima kasih!</p>
                                <p class="text-sm text-gray-300">Tim kami akan menghubungi Anda dalam 1x24 jam via WhatsApp.</p>
                            </div>
                        </div>

                    <form data-lead-form="#service-cta-form" id="service-cta-form" action="{{ route('leads.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="source" value="cta_home">
                        <input type="hidden" name="service_interested" value="{{ $service->name }}">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <div class="lead-field">
                                    <svg class="lead-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="8" r="4" /><path d="M4 20c0-4 4-6 8-6s8 2 8 6" /></svg>
                                    <input name="name" type="text" placeholder="Nama" required class="field-input">
                                </div>
                                <p data-field-error="name" class="field-error"></p>
                            </div>
                            <div>
                                <div class="lead-field">
                                    <svg class="lead-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92Z" /></svg>
                                    <input name="whatsapp" type="text" placeholder="Nomor WhatsApp" required class="field-input">
                                </div>
                                <p data-field-error="whatsapp" class="field-error"></p>
                            </div>
                        </div>
                        <div>
                            <div class="lead-field">
                                <svg class="lead-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2" /><path d="m22 6-10 7L2 6" /></svg>
                                <input name="email" type="email" placeholder="E-mail (opsional)" class="field-input">
                            </div>
                            <p data-field-error="email" class="field-error"></p>
                        </div>
                        <div>
                            <div class="lead-field lead-field--textarea">
                                <svg class="lead-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" /></svg>
                                <textarea name="message" rows="3" placeholder="Pesan (opsional)" class="field-input"></textarea>
                            </div>
                        </div>
                        <button type="submit" data-lead-submit class="lead-submit">Kirim Pesan</button>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.public>
