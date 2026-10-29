@php
    $space = $space ?? false;
    $whatsappUrl = ($settings['contact']['whatsapp_number'] ?? null)
        ? 'https://wa.me/' . $settings['contact']['whatsapp_number'] . '?text=Halo%2C%20saya%20mau%20nanya-nanya'
        : null;
@endphp

<footer class="relative z-10 {{ $space ? 'bg-space-void/80 text-white/50 border-t border-white/10' : 'bg-charcoal text-gray-400' }}">
    @if ($space)
        {{-- Oversized "let's talk" marquee, like a film's closing title card --}}
        <div class="overflow-hidden border-b border-white/10">
            <div class="max-w-[1440px] mx-auto px-4 md:px-8 pt-16 md:pt-24 pb-6 flex items-center justify-between font-mono text-[10px] uppercase tracking-[0.25em] text-white/40">
                <span>[ &infin; ] &mdash; Kontak</span>
                <span class="hidden sm:inline">Respon &lt; 24 jam</span>
            </div>
            <a
                href="{{ $whatsappUrl ?? '/konsultasi' }}"
                @if ($whatsappUrl) target="_blank" rel="noopener" @endif
                class="group block pb-12 md:pb-20"
                aria-label="Mulai proyek bersama kami"
            >
                <div class="flex whitespace-nowrap font-display font-extrabold uppercase text-[18vw] md:text-[11vw] leading-[0.9] tracking-tight" aria-hidden="true">
                    @for ($n = 0; $n < 2; $n++)
                        <span class="footer-marquee__track flex items-center gap-[4vw] pr-[4vw]">
                            <span class="text-white group-hover:text-brand-300 transition-colors">Let's talk</span>
                            <span class="text-brand-400 text-[0.5em]">&#10022;</span>
                            <span class="text-transparent [-webkit-text-stroke:1px_rgb(var(--ink)/0.35)]">Mulai proyek</span>
                            <span class="text-brand-400 text-[0.5em]">&#10022;</span>
                        </span>
                    @endfor
                </div>
            </a>
        </div>
    @endif

    <div class="mx-auto px-4 py-16 md:py-20 grid grid-cols-1 md:grid-cols-4 gap-10 md:gap-8 {{ $space ? 'max-w-[1440px] md:px-8' : 'max-w-7xl' }}">
        <div>
            <h3 class="text-white font-bold text-xl tracking-tight mb-3 {{ $space ? 'font-display' : '' }}">{{ $settings['general']['website_name'] ?? '' }}</h3>
            <p class="text-sm leading-relaxed max-w-xs">{{ $settings['general']['description'] ?? '' }}</p>
        </div>
        <div>
            <h4 class="mb-4 {{ $space ? 'font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400' : 'text-gray-400 font-semibold text-xs uppercase tracking-widest' }}">Layanan</h4>
            <ul class="space-y-2.5 text-sm">
                @foreach ($navServices ?? [] as $service)
                    <li><a href="/{{ $service['slug'] }}" class="hover:text-white transition-colors">{{ $service['name'] }}</a></li>
                @endforeach
            </ul>
        </div>
        <div>
            <h4 class="mb-4 {{ $space ? 'font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400' : 'text-gray-400 font-semibold text-xs uppercase tracking-widest' }}">Kontak</h4>
            <ul class="space-y-2.5 text-sm">
                @if ($settings['contact']['address'] ?? null)
                    <li>{{ $settings['contact']['address'] }}</li>
                @endif
                @if ($settings['contact']['phone'] ?? null)
                    <li>{{ $settings['contact']['phone'] }}</li>
                @endif
                @if ($settings['contact']['email'] ?? null)
                    <li>{{ $settings['contact']['email'] }}</li>
                @endif
            </ul>
        </div>
        <div>
            <h4 class="mb-4 {{ $space ? 'font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400' : 'text-gray-400 font-semibold text-xs uppercase tracking-widest' }}">Ikuti Kami</h4>
            <div class="flex gap-4 text-sm">
                @if ($settings['social']['instagram'] ?? null)
                    <a href="{{ $settings['social']['instagram'] }}" target="_blank" rel="noopener" class="hover:text-white transition-colors">Instagram</a>
                @endif
                @if ($settings['social']['facebook'] ?? null)
                    <a href="{{ $settings['social']['facebook'] }}" target="_blank" rel="noopener" class="hover:text-white transition-colors">Facebook</a>
                @endif
                @if ($space && ($settings['social']['linkedin'] ?? null))
                    <a href="{{ $settings['social']['linkedin'] }}" target="_blank" rel="noopener" class="hover:text-white transition-colors">LinkedIn</a>
                @endif
            </div>
        </div>
    </div>
    <div class="border-t border-white/10 py-5 text-center space-y-1 {{ $space ? 'font-mono uppercase tracking-[0.15em] text-[10px]' : 'text-xs' }}">
        <p>&copy; {{ date('Y') }} {{ $settings['general']['website_name'] ?? '' }}. All rights reserved.</p>
        <p class="{{ $space ? 'text-white/30' : 'text-gray-500' }}">{{ $settings['general']['footer_disclaimer'] ?? '' }}</p>
    </div>
</footer>
