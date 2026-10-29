{{--
    Page header for inner pages on the dark "space" theme: HUD-style mono
    label, oversized display title, slow orbit rings on the right, a giant cropped outline word and a
    diagonal light trail (CSS only, deliberately no grid —
    the WebGL planet is reserved for the homepage hero). Leaves room at the
    top for the fixed transparent space header.
--}}
@props([
    'eyebrow' => '',
    'code' => null,
    'title',
    'subtitle' => '',
    'breadcrumbItems' => null,
])

<section class="relative overflow-hidden pt-36 pb-16 md:pt-44 md:pb-24 border-b border-white/10">
    <!-- Art composition (no grid): giant cropped outline word, a diagonal
         light-trail hairline, off-canvas orbit arcs and a star-chart label. -->
    <span class="space-page-header__word" aria-hidden="true">{{ $eyebrow }}</span>
    <span class="space-page-header__trail" aria-hidden="true"></span>
    <span class="hidden md:block absolute right-[6%] top-[22%] font-mono text-[9px] uppercase tracking-[0.3em] text-white/30 pointer-events-none" aria-hidden="true">RA 14h 29m &middot; DEC &minus;62&deg;</span>
    <div class="space-page-header__orbits pointer-events-none" aria-hidden="true">
        @for ($n = 1; $n <= 5; $n++)
            <span class="orbit" style="--i: {{ $n }}"></span>
        @endfor
    </div>
    <div class="absolute inset-0 pointer-events-none" aria-hidden="true" style="background: radial-gradient(circle at 85% 50%, rgb(var(--lime-400) / 0.10), transparent 45%), radial-gradient(ellipse 50% 60% at 60% 0%, rgb(124 92 255 / 0.10), transparent 70%);"></div>

    <div class="relative max-w-[1440px] mx-auto px-4 md:px-8">
        @if ($breadcrumbItems)
            <x-public.breadcrumbs :items="$breadcrumbItems" variant="space" />
        @endif
        <p data-reveal class="flex items-center gap-3 font-mono text-[10px] md:text-[11px] uppercase tracking-[0.25em] text-white/55 mb-6">
            <span class="text-brand-400">[ {{ $code ?? '00' }} ]</span>
            <span class="h-px w-10 bg-white/25"></span>
            {{ $eyebrow }}
        </p>
        <x-public.headline
            as="h1"
            reveal="load"
            :text="$title"
            class="font-display font-semibold text-[2rem] sm:text-5xl lg:text-6xl xl:text-7xl leading-[1.04] tracking-tight text-white max-w-5xl mb-6 [text-wrap:balance]"
        />
        @if ($subtitle)
            <p data-reveal data-reveal-delay="160" class="text-base md:text-lg text-white/60 leading-relaxed max-w-2xl">{{ $subtitle }}</p>
        @endif
        @if (trim($slot))
            <div data-reveal data-reveal-delay="240" class="mt-8">{{ $slot }}</div>
        @endif
    </div>
</section>
