@props([
    'seoTitle' => null,
    'seoDescription' => null,
    'seoImage' => null,
    'seoType' => 'website',
    'seoJsonLd' => [],
    // 'space' = dark cinematic homepage look (transparent HUD header over a
    // full-screen hero, starfield backdrop, oversized footer CTA). Other
    // pages keep 'light' until they're redesigned too.
    'theme' => 'light',
])

@php
    $space = $theme === 'space';
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @include('partials.public.seo-meta', [
            'seoTitle' => $seoTitle,
            'seoDescription' => $seoDescription,
            'seoImage' => $seoImage,
            'seoType' => $seoType,
            'seoJsonLd' => $seoJsonLd,
        ])

        @php
            $customFavicon = \App\Models\Setting::get('favicon');
            $customFaviconIsIco = $customFavicon && str_ends_with(strtolower($customFavicon), '.ico');
        @endphp
        @if ($customFavicon)
            <link rel="icon" href="{{ $customFavicon }}" type="{{ $customFaviconIsIco ? 'image/x-icon' : 'image/png' }}">
            @unless ($customFaviconIsIco)
                <link rel="apple-touch-icon" href="{{ $customFavicon }}">
            @endunless
        @else
            <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
            <link rel="icon" href="{{ asset('favicon-48x48.png') }}" type="image/png" sizes="48x48">
            <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">
        @endif

        {{-- Fonts are self-hosted (resources/css/fonts.css, public/fonts). --}}

        @php
            $gaMeasurementId = \App\Models\Setting::get('ga_measurement_id');
        @endphp
        @if ($gaMeasurementId)
            <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaMeasurementId }}"></script>
            <script nonce="{{ app('csp-nonce') }}">
                window.dataLayer = window.dataLayer || [];
                function gtag() { dataLayer.push(arguments); }
                gtag('js', new Date());
                gtag('config', '{{ $gaMeasurementId }}');
            </script>
        @endif

        {{-- Space theme light/dark: applied before first paint (no flash). Dark is the
             default (it is the brand look); a visitor's explicit choice is remembered. --}}
        <script nonce="{{ app('csp-nonce') }}">
            (function () {
                var t = "dark";
                try { t = localStorage.getItem("theme") === "light" ? "light" : "dark"; } catch (e) {}
                document.documentElement.setAttribute("data-theme", t);
            })();
        </script>
        @stack('head')
        @vite(['resources/js/blade-app.js'])
    </head>
    <body class="font-sans antialiased {{ $space ? 'theme-space bg-space-void' : '' }}">
        <div class="min-h-screen flex flex-col {{ $space ? 'theme-space bg-space-void text-white' : 'bg-white' }}">
            @if ($space)
                <canvas class="fixed inset-0 z-0 w-full h-full pointer-events-none" data-starfield aria-hidden="true"></canvas>
            @endif

            @include('partials.public.navbar', ['space' => $space])

            <main class="flex-1 relative z-10">
                {{ $slot }}
            </main>

            @if ($settings['contact']['whatsapp_number'] ?? null)
                <a
                    href="https://wa.me/{{ $settings['contact']['whatsapp_number'] }}?text=Halo%2C%20saya%20mau%20nanya-nanya"
                    target="_blank"
                    rel="noopener"
                    aria-label="Chat WhatsApp"
                    class="fixed bottom-5 right-5 z-50 flex h-16 w-16 items-center justify-center rounded-full bg-white text-[#25D366] shadow-lg border-2 border-[#25D366] hover:bg-[#f0fdf4] hover:scale-105 transition-all overflow-hidden {{ $space ? 'shadow-[0_0_40px_-6px_rgba(37,211,102,0.6)]' : '' }}"
                >
                    @if ($mascots['whatsapp_widget']['image_path'] ?? null)
                        <img
                            src="{{ $mascots['whatsapp_widget']['image_path'] }}"
                            alt="{{ $mascots['whatsapp_widget']['alt_text'] ?? 'Chat WhatsApp' }}"
                            width="64" height="64"
                            class="h-full w-full object-cover"
                        >
                    @else
                        <svg viewBox="0 0 32 32" class="h-9 w-9" fill="currentColor" aria-hidden="true">
                            <path d="M16.001 3C9.373 3 4 8.373 4 15c0 2.31.64 4.47 1.75 6.31L4 29l7.86-1.71A11.94 11.94 0 0 0 16.001 27C22.63 27 28 21.627 28 15S22.63 3 16.001 3Zm0 21.75c-1.93 0-3.73-.53-5.28-1.45l-.38-.22-4.66 1.01 1.02-4.53-.25-.4A9.72 9.72 0 0 1 5.25 15c0-5.93 4.82-10.75 10.75-10.75S26.75 9.07 26.75 15 21.93 24.75 16.001 24.75Zm5.98-8.09c-.33-.16-1.93-.95-2.23-1.06-.3-.11-.52-.16-.74.16-.22.33-.85 1.06-1.04 1.27-.19.22-.38.24-.71.08-.33-.16-1.38-.51-2.63-1.62-.97-.87-1.63-1.94-1.82-2.27-.19-.33-.02-.5.14-.67.15-.15.33-.38.49-.57.16-.19.22-.33.33-.55.11-.22.05-.41-.03-.57-.08-.16-.74-1.78-1.01-2.44-.27-.64-.54-.55-.74-.56-.19-.01-.41-.01-.63-.01-.22 0-.57.08-.87.41-.3.33-1.14 1.11-1.14 2.71 0 1.6 1.17 3.14 1.33 3.36.16.22 2.3 3.51 5.57 4.92.78.34 1.38.54 1.86.69.78.25 1.49.21 2.05.13.63-.09 1.93-.79 2.2-1.55.27-.76.27-1.41.19-1.55-.08-.14-.3-.22-.63-.38Z"/>
                        </svg>
                    @endif
                </a>
            @endif

            @include('partials.public.footer', ['space' => $space])
        </div>
    </body>
</html>
