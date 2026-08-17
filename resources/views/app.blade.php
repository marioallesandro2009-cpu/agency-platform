<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php
            $seoSiteName = \App\Models\Setting::get('website_name') ?: config('app.name', 'Laravel');
            $seoTagline = \App\Models\Setting::get('tagline');
            $seoDescription = \App\Models\Setting::get('description');
            $seoOgImage = \App\Models\Setting::get('og_image');
            if ($seoOgImage && !preg_match('/^https?:\/\//', $seoOgImage)) {
                $seoOgImage = url($seoOgImage);
            }
            $seoTitle = $seoTagline ? "{$seoSiteName} - {$seoTagline}" : $seoSiteName;
        @endphp

        {{--
            Rendered server-side (not just via Inertia's client-side <Head>) because
            link-preview crawlers (WhatsApp, Facebook, Twitter) don't execute JS —
            they only ever see this raw blade output. The `inertia="<key>"` attributes
            match the `head-key`s in SeoHead.vue, so Inertia's Head component replaces
            each tag in place once Vue mounts instead of appending a duplicate —
            per-page SeoHead.vue titles/meta still work for real visitors.
        --}}
        <title inertia>{{ $seoTitle }}</title>
        <meta inertia="description" name="description" content="{{ $seoDescription }}">

        <meta inertia="og-site-name" property="og:site_name" content="{{ $seoSiteName }}">
        <meta inertia="og-title" property="og:title" content="{{ $seoTitle }}">
        <meta inertia="og-description" property="og:description" content="{{ $seoDescription }}">
        <meta inertia="og-type" property="og:type" content="website">
        <meta inertia="og-url" property="og:url" content="{{ url()->current() }}">
        @if ($seoOgImage)
            <meta inertia="og-image" property="og:image" content="{{ $seoOgImage }}">
        @endif

        <meta inertia="twitter-card" name="twitter:card" content="summary_large_image">
        <meta inertia="twitter-title" name="twitter:title" content="{{ $seoTitle }}">
        <meta inertia="twitter-description" name="twitter:description" content="{{ $seoDescription }}">
        @if ($seoOgImage)
            <meta inertia="twitter-image" name="twitter:image" content="{{ $seoOgImage }}">
        @endif

        <link rel="icon" href="{{ \App\Models\Setting::get('favicon') ?: asset('favicon.ico') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=raleway:400,500,600,700,800&display=swap" rel="stylesheet" />

        {{--
            Google Analytics only loads when an admin has set a Measurement ID
            in Settings, and never on the admin/auth surface (only staff ever
            use these routes, no public visitor does) — so admin activity
            doesn't pollute visitor analytics. Empty ID = GA fully inert.
        --}}
        @php
            $gaMeasurementId = \App\Models\Setting::get('ga_measurement_id');
            $isAdminSurface = request()->is(
                'admin', 'admin/*', 'dashboard', 'login', 'profile',
                'forgot-password', 'reset-password/*', 'confirm-password',
                'verify-email', 'verify-email/*'
            );
        @endphp
        @if ($gaMeasurementId && ! $isAdminSurface)
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaMeasurementId }}"></script>
            <script>
                window.dataLayer = window.dataLayer || [];
                function gtag() { dataLayer.push(arguments); }
                gtag('js', new Date());
                gtag('config', '{{ $gaMeasurementId }}');
            </script>
        @endif

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
