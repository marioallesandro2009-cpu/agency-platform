{{--
    Display headline with two signatures of the space theme:
    - the last word is set as an editorial accent (italic serif, lime),
      contrasting the wide Unbounded face;
    - words rise out of a mask one by one. reveal="scroll" (default) is
      armed by blade-app.js (initSplitHeadlines) when it scrolls into view;
      reveal="load" is a pure-CSS entrance for above-the-fold headlines, so
      they never flash in, vanish and re-animate while JS loads. Without JS
      (scroll) or with reduced motion the text simply renders as-is.
--}}
@props([
    'text' => '',
    'as' => 'h2',
    'reveal' => 'scroll',
])

@php
    $words = preg_split('/\s+/', trim((string) $text), -1, PREG_SPLIT_NO_EMPTY);
    $last = count($words) - 1;
@endphp

<{{ $as }} data-split="{{ $reveal }}" {{ $attributes }}>
    <span class="sr-only">{{ $text }}</span>
    <span aria-hidden="true">
        @foreach ($words as $i => $word)
            <span class="split-word" style="--i: {{ $i }}"><span class="{{ $i === $last && $last > 0 ? 'headline-accent' : '' }}">{{ $word }}</span></span>{{ $i < $last ? ' ' : '' }}
        @endforeach
    </span>
</{{ $as }}>
