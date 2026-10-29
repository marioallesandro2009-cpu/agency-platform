@props(['items' => [], 'variant' => 'light'])

@php
    $breadcrumbJsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => collect($items)->values()->map(fn ($item, $index) => array_filter([
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $item['label'],
            'item' => isset($item['href']) ? url($item['href']) : null,
        ]))->all(),
    ];
@endphp

<script type="application/ld+json" nonce="{{ app('csp-nonce') }}">{!! json_encode($breadcrumbJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

@php
    // dark = on the old charcoal page header; space = HUD-style mono trail on the dark space theme.
    $onDark = in_array($variant, ['dark', 'space'], true);
@endphp

<nav aria-label="Breadcrumb" class="{{ $onDark ? 'mb-6' : 'max-w-6xl mx-auto px-4 pt-6' }}">
    <ol class="flex flex-wrap items-center gap-1.5 {{ $variant === 'space' ? 'font-mono text-[10px] uppercase tracking-[0.2em] text-white/45' : ($onDark ? 'text-sm text-gray-300' : 'text-sm text-gray-500') }}">
        @foreach ($items as $index => $item)
            <li class="flex items-center gap-1.5">
                @if (!empty($item['href']))
                    <a href="{{ $item['href'] }}" class="{{ $onDark ? 'hover:text-white' : 'hover:text-brand-600' }}">{{ $item['label'] }}</a>
                @else
                    <span class="font-medium {{ $onDark ? 'text-brand-400' : 'text-gray-700' }}" aria-current="page">{{ $item['label'] }}</span>
                @endif
                @if ($index < count($items) - 1)
                    <svg class="w-3.5 h-3.5 flex-shrink-0 {{ $onDark ? 'text-gray-500' : 'text-gray-300' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                    </svg>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
