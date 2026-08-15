<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

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
