@php
    $space = $space ?? false;
    $homeItem = collect($navMenu ?? [])->firstWhere('url', '/');
    $menuItems = collect($navMenu ?? [])->reject(fn ($item) => $item['url'] === '/');
    // Software Custom has no price list: its 'Harga' goes to contact, not to the website packages.
    if ($corporate ?? false) {
        $menuItems = $menuItems->map(fn ($item) => $item['url'] === '/harga' ? ['label' => $item['label'], 'url' => '/kontak'] : $item);
    }
    // A service that already has its own top-level menu link must not repeat in the dropdown.
    $topLevelUrls = $menuItems->pluck('url')->all();
    $dropdownServices = collect($navServices ?? [])->reject(fn ($service) => in_array('/' . $service['slug'], $topLevelUrls, true));
    $navLink = $space
        ? 'nav-underline font-mono text-[11px] uppercase tracking-[0.18em] text-white/60 hover:text-white'
        : 'nav-underline text-xs font-semibold uppercase tracking-wide text-gray-600 hover:text-brand-600';
    $mobileLink = $space ? 'text-white/80' : 'text-gray-700';
@endphp

@unless ($space)
    <div class="bg-charcoal text-gray-300 text-xs">
        <div class="max-w-7xl mx-auto px-4 py-2 flex flex-col sm:flex-row justify-between gap-1">
            <div class="flex gap-4">
                @if ($settings['contact']['phone'] ?? null)
                    <span>Telp: {{ $settings['contact']['phone'] }}</span>
                @endif
                @if ($settings['contact']['email'] ?? null)
                    <span>Email: {{ $settings['contact']['email'] }}</span>
                @endif
            </div>
            <div class="flex gap-3">
                @if ($settings['social']['instagram'] ?? null)
                    <a href="{{ $settings['social']['instagram'] }}" target="_blank" rel="noopener" class="hover:text-white">Instagram</a>
                @endif
                @if ($settings['social']['facebook'] ?? null)
                    <a href="{{ $settings['social']['facebook'] }}" target="_blank" rel="noopener" class="hover:text-white">Facebook</a>
                @endif
                @if ($settings['social']['linkedin'] ?? null)
                    <a href="{{ $settings['social']['linkedin'] }}" target="_blank" rel="noopener" class="hover:text-white">LinkedIn</a>
                @endif
            </div>
        </div>
    </div>
@endunless

<header
    @if ($space) data-space-header @endif
    class="navbar-in z-40 {{ $space ? 'space-header fixed inset-x-0 top-0 border-b' : 'sticky top-0 bg-white/95 backdrop-blur-sm border-b border-gray-100' }}"
>
    <div class="mx-auto px-4 h-16 md:h-[4.5rem] flex items-center justify-between gap-4 {{ $space ? 'max-w-[1440px] md:px-8' : 'max-w-7xl' }}">
        <a href="/" class="flex items-center gap-3 shrink-0">
            @if ($settings['general']['logo'] ?? null)
                <img src="{{ \App\Support\Img::url($settings['general']['logo'], 480) }}" width="180" height="48" class="h-10 md:h-11 w-auto {{ $space ? 'space-logo' : '' }}" alt="{{ $settings['general']['website_name'] ?? '' }}">
            @else
                <span class="font-bold text-xl tracking-tight {{ $space ? 'font-display text-white' : 'text-gray-900' }}">{{ $settings['general']['website_name'] ?? '' }}</span>
            @endif
            @if ($space)
                <span class="hidden 2xl:inline font-mono text-[10px] uppercase tracking-[0.2em] text-white/40">// Digital Studio</span>
            @endif
        </a>

        <nav class="hidden flex-1 items-center justify-center {{ $space ? 'min-[1120px]:flex gap-4 xl:gap-5 2xl:gap-7 whitespace-nowrap' : 'md:flex gap-8' }}">
            @if ($homeItem)
                <a href="{{ $homeItem['url'] }}" class="{{ $navLink }}">@if ($space)<span class="text-brand-400 mr-1.5">&bull;</span>@endif{{ $homeItem['label'] }}</a>
            @endif

            <div class="relative group">
                <button type="button" aria-controls="services-dropdown" class="flex items-center gap-1.5 {{ $navLink }}">
                    Jasa &amp; Layanan
                    <span aria-hidden="true" class="text-xs normal-case transition-transform duration-200 group-hover:-rotate-180">v</span>
                </button>
                <div id="services-dropdown" class="absolute left-0 top-full pt-3 w-72 hidden group-hover:block group-focus-within:block">
                    <div class="rounded-lg py-2 {{ $space ? 'bg-space-deep/95 backdrop-blur-xl border border-white/10 shadow-2xl' : 'bg-white shadow-elevated border border-black/10' }}">
                        @foreach ($dropdownServices as $service)
                            <a href="/{{ $service['slug'] }}" class="block px-4 py-2.5 text-sm {{ $space ? 'text-white/70 hover:bg-white/5 hover:text-brand-300' : 'text-gray-700 hover:bg-gray-50 hover:text-brand-600' }}">
                                {{ $service['name'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            @foreach ($menuItems as $item)
                <a href="{{ $item['url'] }}" class="{{ $navLink }}">{{ $item['label'] }}</a>
            @endforeach
        </nav>

        <div class="flex items-center gap-3 shrink-0">
            @if ($space)
            <button
                type="button"
                data-theme-toggle
                class="theme-toggle w-10 h-10 shrink-0 rounded-full border border-white/15 bg-white/5 text-white/80 hover:text-white hover:border-white/40 flex items-center justify-center transition-colors"
                aria-label="Ganti mode terang/gelap"
                title="Ganti mode terang/gelap"
            >
                <svg class="theme-toggle__sun w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" aria-hidden="true"><circle cx="12" cy="12" r="4" /><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41" /></svg>
                <svg class="theme-toggle__moon w-[18px] h-[18px]" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z" /></svg>
            </button>
            @endif
            @if ($settings['contact']['whatsapp_number'] ?? null)
                <div class="hidden {{ $space ? 'xl:block' : 'md:block' }}">
                    @if ($space)
                        <a
                            href="https://wa.me/{{ $settings['contact']['whatsapp_number'] }}?text=Halo%2C%20saya%20mau%20nanya-nanya"
                            target="_blank" rel="noopener"
                            class="btn-space btn-space--glass"
                        >
                            Konsultasi Gratis <span class="btn-space__arrow" aria-hidden="true">&rarr;</span>
                        </a>
                    @else
                        <a
                            href="https://wa.me/{{ $settings['contact']['whatsapp_number'] }}?text=Halo%2C%20saya%20mau%20nanya-nanya"
                            target="_blank" rel="noopener"
                            class="inline-flex items-center justify-center gap-1.5 transition-all duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 px-6 py-3 text-sm font-semibold uppercase tracking-wide rounded-md hover:-translate-y-0.5 active:scale-[0.98] active:translate-y-0 bg-brand-600 text-white hover:bg-brand-700"
                        >
                            Konsultasi Gratis
                        </a>
                    @endif
                </div>
            @endif

            <button data-nav-toggle aria-expanded="false" aria-controls="mobile-menu-panel" aria-label="Menu" class="{{ $space ? 'min-[1120px]:hidden' : 'md:hidden' }} p-1 {{ $space ? 'text-white' : 'text-gray-700' }}">
                <svg data-icon-open class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg data-icon-close class="hidden w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>

    <div data-nav-panel id="mobile-menu-panel" class="hidden border-t px-4 py-3 space-y-1 {{ $space ? 'min-[1120px]:hidden border-white/10' : 'md:hidden' }}">
        @if ($homeItem)
            <a href="{{ $homeItem['url'] }}" class="block text-sm font-medium py-1.5 {{ $mobileLink }}">{{ $homeItem['label'] }}</a>
        @endif

        <button data-mobile-services-toggle type="button" aria-expanded="false" aria-controls="mobile-services-panel" class="w-full flex items-center justify-between text-sm font-medium py-1.5 {{ $mobileLink }}">
            Jasa &amp; Layanan
            <span data-caret aria-hidden="true" class="text-xs">v</span>
        </button>
        <div data-mobile-services-panel id="mobile-services-panel" class="hidden pl-3 space-y-1 border-l ml-1 {{ $space ? 'border-white/10' : '' }}">
            @foreach ($dropdownServices as $service)
                <a href="/{{ $service['slug'] }}" class="block text-sm py-1 {{ $space ? 'text-white/60' : 'text-gray-600' }}">{{ $service['name'] }}</a>
            @endforeach
        </div>

        @foreach ($menuItems as $item)
            <a href="{{ $item['url'] }}" class="block text-sm font-medium py-1.5 {{ $mobileLink }}">{{ $item['label'] }}</a>
        @endforeach
    </div>
</header>
