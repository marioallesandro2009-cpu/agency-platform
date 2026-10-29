{{--
    Section heading for the dark "space" pages: HUD-style mono index label
    ("[ 03 ] LAYANAN") over a hairline rule, then an oversized display
    headline (x-public.headline: word-by-word reveal + serif accent word).
--}}
@props([
    'index' => null,
    'label' => '',
    'title' => '',
    'center' => false,
])

<div {{ $attributes->class(['mb-12 md:mb-16', 'text-center max-w-3xl mx-auto' => $center]) }}>
    <div data-reveal class="flex items-center gap-4 font-mono text-[10px] md:text-[11px] uppercase tracking-[0.25em] text-white/45 mb-6 {{ $center ? 'justify-center' : '' }}">
        @if ($index !== null && $index !== false)
            <span class="text-brand-400">[ {{ str_pad($index, 2, '0', STR_PAD_LEFT) }} ]</span>
        @endif
        <span>{{ $label }}</span>
        @unless ($center)
            <span class="h-px flex-1 bg-gradient-to-r from-white/20 to-transparent"></span>
        @endunless
    </div>
    <x-public.headline
        :text="$title"
        class="font-display font-semibold text-3xl sm:text-4xl md:text-5xl lg:text-6xl leading-[1.04] tracking-tight text-white [text-wrap:balance]"
    />
    {{ $slot }}
</div>
