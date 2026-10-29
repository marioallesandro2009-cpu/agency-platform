@props([
    'eyebrow' => '',
    'title',
    'subtitle' => '',
    'color1' => '#4d7c0f',
    'color2' => '#a3e635',
    'color3' => '#1a1a1a',
    'breadcrumbItems' => null,
])

<section class="page-header relative isolate overflow-hidden bg-charcoal text-white py-14 sm:py-20 md:py-28">
    <div
        class="absolute inset-0 z-0"
        data-vue-island="ScannerBackground"
        data-props='{{ json_encode(["color1" => $color1, "color2" => $color2, "color3" => $color3]) }}'
    ></div>

    <div class="absolute inset-0 z-10" aria-hidden="true" style="background: linear-gradient(115deg, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0.35) 45%, rgba(0,0,0,0.15) 100%);"></div>

    <div class="relative z-20 max-w-7xl mx-auto px-4">
        @if ($breadcrumbItems)
            <x-public.breadcrumbs :items="$breadcrumbItems" variant="dark" />
        @endif
        <div class="max-w-3xl">
            @if ($eyebrow)
                <p data-reveal class="text-brand-400 font-semibold text-xs uppercase tracking-widest mb-4">{{ $eyebrow }}</p>
            @endif
            <h1 data-reveal data-reveal-delay="80" class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold leading-[1.05] tracking-tight mb-4 sm:mb-6 text-left [text-wrap:balance]">
                {{ $title }}
            </h1>
            @if ($subtitle)
                <p data-reveal data-reveal-delay="160" class="text-gray-300 text-lg md:text-xl text-left max-w-2xl">{{ $subtitle }}</p>
            @endif
            @isset($slot)
                @if (trim($slot))
                    <div data-reveal data-reveal-delay="240" class="text-left mt-6">
                        {{ $slot }}
                    </div>
                @endif
            @endisset
        </div>
    </div>
</section>
