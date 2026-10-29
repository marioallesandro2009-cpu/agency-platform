{{--
    Space-theme "talk to us" block: heading on the left, the short lead form
    on the right, in a glass panel. Submitted via fetch by blade-app.js
    (initLeadForms); the success box replaces the form in place.
--}}
@props([
    'formId' => 'contact-cta-form',
    'eyebrow' => 'Mulai Sekarang',
    'title' => 'Siap Membangun Website Impian Anda?',
    'description' => '',
    'source' => 'cta_home',
    'serviceInterested' => null,
])

<section class="contact-cta relative pb-24 md:pb-32 overflow-hidden">
    <div class="max-w-[1440px] mx-auto px-4 md:px-8 relative">
        <div class="relative overflow-hidden rounded-3xl border border-white/10 bg-space-deep/70 px-6 py-12 md:px-14 md:py-16">
            <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
            <div class="relative grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-start">
                <div class="lg:col-span-5">
                    <p data-reveal class="flex items-center gap-3 font-mono text-[10px] md:text-[11px] uppercase tracking-[0.25em] text-white/50 mb-5">
                        <span class="text-brand-400">[ &rarr; ]</span>
                        {{ $eyebrow }}
                    </p>
                    <h2 data-reveal data-reveal-delay="80" class="font-display font-semibold text-3xl md:text-5xl leading-[1.05] tracking-tight text-white mb-5 [text-wrap:balance]">{{ $title }}</h2>
                    @if ($description)
                        <p data-reveal data-reveal-delay="120" class="text-white/60">{{ $description }}</p>
                    @endif
                </div>

                <div class="lg:col-span-7">
                    <div id="{{ $formId }}-success" class="hidden flex items-start gap-3 bg-white/5 border border-white/10 rounded-lg p-5" role="status">
                        <span class="text-brand-400 text-xl leading-none shrink-0 mt-0.5" aria-hidden="true">&#10022;</span>
                        <div>
                            <p class="font-semibold text-white mb-1">Terima kasih!</p>
                            <p class="text-sm text-white/60">Tim kami akan menghubungi Anda dalam 1x24 jam via WhatsApp.</p>
                        </div>
                    </div>

                    <form data-lead-form="#{{ $formId }}" id="{{ $formId }}" action="{{ route('leads.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="source" value="{{ $source }}">
                        @if ($serviceInterested)
                            <input type="hidden" name="service_interested" value="{{ $serviceInterested }}">
                        @endif
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <div class="lead-field">
                                    <svg class="lead-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="8" r="4" /><path d="M4 20c0-4 4-6 8-6s8 2 8 6" /></svg>
                                    <input name="name" type="text" placeholder="Nama" aria-label="Nama" required class="field-input">
                                </div>
                                <p data-field-error="name" class="field-error"></p>
                            </div>
                            <div>
                                <div class="lead-field">
                                    <svg class="lead-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.127.96.362 1.903.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.338 1.85.573 2.81.7A2 2 0 0 1 22 16.92Z" /></svg>
                                    <input name="whatsapp" type="text" inputmode="tel" placeholder="Nomor WhatsApp" aria-label="Nomor WhatsApp" required class="field-input">
                                </div>
                                <p data-field-error="whatsapp" class="field-error"></p>
                            </div>
                        </div>
                        <div>
                            <div class="lead-field">
                                <svg class="lead-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="4" width="20" height="16" rx="2" /><path d="m22 6-10 7L2 6" /></svg>
                                <input name="email" type="email" placeholder="E-mail (opsional)" aria-label="E-mail (opsional)" class="field-input">
                            </div>
                            <p data-field-error="email" class="field-error"></p>
                        </div>
                        <div>
                            <div class="lead-field lead-field--textarea">
                                <svg class="lead-icon" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" /></svg>
                                <textarea name="message" rows="3" placeholder="Pesan (opsional)" aria-label="Pesan (opsional)" class="field-input"></textarea>
                            </div>
                        </div>
                        <button type="submit" data-lead-submit class="lead-submit">Kirim Pesan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
