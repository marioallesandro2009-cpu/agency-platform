@php
    $siteName = $settings['general']['website_name'] ?? 'Website';
    $fullTitle = $seoTitle ? "{$seoTitle} | {$siteName}" : $siteName;
    $metaDescription = $seoDescription ?: ($settings['general']['description'] ?? '');
    $canonicalUrl = url()->current();

    $ogImage = $seoImage ?: ($settings['general']['og_image'] ?? null);
    if ($ogImage && !preg_match('/^https?:\/\//', $ogImage)) {
        $ogImage = url($ogImage);
    }

    $organizationJsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => $settings['general']['website_name'] ?? null,
        'description' => $settings['general']['description'] ?? null,
        'url' => url('/'),
        'logo' => $settings['general']['logo'] ?? null,
        'email' => $settings['contact']['email'] ?? null,
        'telephone' => $settings['contact']['phone'] ?? null,
        'address' => ($settings['contact']['address'] ?? null)
            ? ['@type' => 'PostalAddress', 'addressLocality' => $settings['contact']['address']]
            : null,
        'sameAs' => array_values(array_filter($settings['social'] ?? [])) ?: null,
    ];
    $organizationJsonLd = array_filter($organizationJsonLd, fn ($v) => $v !== null);
    if (isset($organizationJsonLd['logo']) && !preg_match('/^https?:\/\//', $organizationJsonLd['logo'])) {
        $organizationJsonLd['logo'] = url($organizationJsonLd['logo']);
    }

    // Site name shown by search engines next to results; only needed on the homepage.
    $websiteJsonLd = request()->is('/') ? [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => $siteName,
        'url' => url('/'),
        'inLanguage' => 'id-ID',
    ] : null;
@endphp

<title>{{ $fullTitle }}</title>
<meta name="description" content="{{ $metaDescription }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:title" content="{{ $fullTitle }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:type" content="{{ $seoType }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
@if ($ogImage)
    <meta property="og:image" content="{{ $ogImage }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $fullTitle }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
@if ($ogImage)
    <meta name="twitter:image" content="{{ $ogImage }}">
@endif

<script type="application/ld+json" nonce="{{ app('csp-nonce') }}">{!! json_encode($organizationJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

@if ($websiteJsonLd)
    <script type="application/ld+json" nonce="{{ app('csp-nonce') }}">{!! json_encode($websiteJsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endif

@foreach ($seoJsonLd as $block)
    <script type="application/ld+json" nonce="{{ app('csp-nonce') }}">{!! json_encode($block, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
@endforeach
