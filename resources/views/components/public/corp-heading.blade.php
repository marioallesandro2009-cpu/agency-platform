{{--
    Section heading for the corporate (accent="blue") pages: report-style
    section number over a solid rule, then a plain, heavy sans headline.
--}}
@props([
    'index' => null,
    'label' => '',
    'title' => '',
])

<div {{ $attributes->class(['mb-7 md:mb-9']) }}>
    <p data-reveal class="flex items-center gap-3 border-t-2 border-brand-400 pt-3 font-mono text-[11px] uppercase tracking-[0.14em] text-white/60 mb-6">
        @if ($index)
            <span class="text-brand-300">{{ $index }}</span>
        @endif
        <span>{{ $label }}</span>
    </p>
    <h2 data-reveal class="font-display font-bold text-2xl md:text-4xl leading-[1.12] text-white max-w-4xl [text-wrap:balance]">{{ $title }}</h2>
    {{ $slot }}
</div>
