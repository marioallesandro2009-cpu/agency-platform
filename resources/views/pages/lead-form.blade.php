@php
    $steps = ['Data Diri', 'Kebutuhan', 'Detail', 'Ringkasan'];
    $pad = fn ($n) => str_pad($n, 2, '0', STR_PAD_LEFT);
    $budgetOptions = [
        '' => 'Belum tahu / masih survei',
        '< Rp1 Juta' => '< Rp1 Juta',
        'Rp1 - 3 Juta' => 'Rp1 - 3 Juta',
        'Rp3 - 7 Juta' => 'Rp3 - 7 Juta',
        'Rp7 - 15 Juta' => 'Rp7 - 15 Juta',
        '> Rp15 Juta' => '> Rp15 Juta',
    ];
    $fieldClass = 'w-full rounded-xl border border-white/15 bg-white/[0.04] px-4 py-3 text-sm text-white placeholder:text-white/30 focus:outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-400/30';
    $labelClass = 'block font-mono text-[10px] uppercase tracking-[0.2em] text-white/55 mb-2';
    $contact = $settings['contact'] ?? [];
@endphp

<x-layouts.public theme="space" :seo-title="$title" :seo-description="$subtitle">
    <x-public.space-page-header eyebrow="Konsultasi" code="CTA" :title="$title" :subtitle="$subtitle" />

    <section class="max-w-[1440px] mx-auto px-4 md:px-8 py-16 md:py-24">
        {{-- Thank-you state, revealed by blade-app.js after a successful submit --}}
        <div data-lead-success class="hidden relative overflow-hidden rounded-3xl border border-brand-400/30 bg-space-deep text-center px-6 py-20 max-w-3xl mx-auto" role="status">
            <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
            <div class="relative w-16 h-16 rounded-full border border-brand-400 text-brand-400 text-2xl flex items-center justify-center mx-auto mb-6 shadow-[0_0_40px_-6px_rgb(var(--lime-400)/0.6)]" aria-hidden="true">&#10022;</div>
            <p class="relative font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400 mb-3">Transmisi diterima</p>
            <h2 class="relative font-display font-semibold text-3xl text-white tracking-tight mb-3">Terima Kasih!</h2>
            <p data-lead-success-message class="relative text-white/60 max-w-sm mx-auto">Tim kami akan menghubungi Anda dalam 1x24 jam via WhatsApp.</p>
        </div>

        <div data-lead-body class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16">
            {{-- Left: direct contact + step rail --}}
            <aside class="lg:col-span-4">
                <div class="lg:sticky lg:top-28 space-y-10">
                    @if ($context === 'kontak')
                        <div>
                            <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-white/45 mb-4">Hubungi langsung</p>
                            <div class="flex flex-wrap gap-2">
                                @if ($contact['whatsapp_number'] ?? null)
                                    <a href="https://wa.me/{{ $contact['whatsapp_number'] }}?text=Halo%2C%20saya%20mau%20nanya-nanya" target="_blank" rel="noopener" class="btn-space btn-space--pill">Chat WhatsApp <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
                                @endif
                                @if ($contact['phone'] ?? null)
                                    <a href="tel:{{ $contact['phone'] }}" class="btn-space btn-space--glass">Telepon <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
                                @endif
                                @if ($contact['email'] ?? null)
                                    <a href="mailto:{{ $contact['email'] }}" class="btn-space btn-space--glass">Email <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></a>
                                @endif
                            </div>
                            <p class="text-sm text-white/45 mt-5">Atau isi formulir dan kami akan menghubungi Anda lewat WhatsApp.</p>
                        </div>
                    @endif

                    {{-- Step rail: numbers in every state (no check marks); state via data-state --}}
                    <ol class="step-rail" aria-label="Progress pengisian form">
                        @foreach ($steps as $i => $step)
                            <li class="step-rail__item" data-step-node="{{ $i + 1 }}" data-state="{{ $i === 0 ? 'active' : 'upcoming' }}" @if ($i === 0) aria-current="step" @endif>
                                <span class="step-rail__node">{{ $pad($i + 1) }}</span>
                                <span class="step-rail__label">{{ $step }}</span>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </aside>

            {{-- Right: the wizard (blade-app.js initLeadWizard) --}}
            <form
                data-lead-wizard
                action="{{ route('leads.store') }}"
                method="POST"
                novalidate
                class="lg:col-span-8 rounded-2xl border border-white/10 bg-space-deep/60 backdrop-blur-md p-6 md:p-10"
            >
                @csrf
                <input type="hidden" name="source" value="{{ $context }}">
                <input type="hidden" name="pricing_category" value="{{ $selectedPackage['category'] ?? '' }}">
                <input type="hidden" name="pricing_tier" value="{{ $selectedPackage['tier'] ?? '' }}">
                <input type="hidden" name="budget_estimate" value="" data-budget-input>

                <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-white/40 mb-2">
                    Langkah <span class="text-brand-400" data-step-counter>01</span> / {{ $pad(count($steps)) }}
                </p>

                {{-- Step 1: Data Diri --}}
                <div data-step="1" class="space-y-5">
                    <h2 class="font-display font-semibold text-2xl md:text-3xl text-white tracking-tight mb-6">Data Diri</h2>
                    <div>
                        <label for="lead-name" class="{{ $labelClass }}">Nama Perusahaan <span class="text-brand-400">*</span></label>
                        <input id="lead-name" name="name" type="text" required autocomplete="organization" class="{{ $fieldClass }}">
                        <p data-field-error="name" class="text-xs text-red-400 mt-1.5"></p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="lead-whatsapp" class="{{ $labelClass }}">Nomor WA <span class="text-brand-400">*</span></label>
                            <input id="lead-whatsapp" name="whatsapp" type="text" inputmode="tel" required placeholder="08xxxxxxxxxx" autocomplete="tel" class="{{ $fieldClass }}">
                            <p data-field-error="whatsapp" class="text-xs text-red-400 mt-1.5"></p>
                        </div>
                        <div>
                            <label for="lead-email" class="{{ $labelClass }}">Email (opsional)</label>
                            <input id="lead-email" name="email" type="email" autocomplete="email" class="{{ $fieldClass }}">
                            <p data-field-error="email" class="text-xs text-red-400 mt-1.5"></p>
                        </div>
                    </div>
                </div>

                {{-- Step 2: Kebutuhan --}}
                <div data-step="2" class="hidden space-y-5">
                    <h2 class="font-display font-semibold text-2xl md:text-3xl text-white tracking-tight mb-6">Kebutuhan</h2>
                    <div>
                        <label for="lead-service" class="{{ $labelClass }}">Jenis Layanan <span class="text-brand-400">*</span></label>
                        <input id="lead-service" name="service_interested" type="text" required value="{{ $serviceInterested }}" placeholder="mis. Website Company Profile" class="{{ $fieldClass }}">
                        <p data-field-error="service_interested" class="text-xs text-red-400 mt-1.5"></p>
                    </div>
                    <fieldset>
                        <legend class="{{ $labelClass }}">Estimasi Anggaran</legend>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($budgetOptions as $value => $label)
                                <button type="button" data-budget="{{ $value }}" aria-pressed="{{ $value === '' ? 'true' : 'false' }}" class="space-tab px-4 py-2 rounded-full text-sm">{{ $label }}</button>
                            @endforeach
                        </div>
                    </fieldset>
                </div>

                {{-- Step 3: Detail --}}
                <div data-step="3" class="hidden space-y-5">
                    <h2 class="font-display font-semibold text-2xl md:text-3xl text-white tracking-tight mb-6">Detail</h2>
                    <div>
                        <label for="lead-address" class="{{ $labelClass }}">Detail Alamat (opsional, boleh dilewati)</label>
                        <textarea id="lead-address" name="address" rows="2" placeholder="Alamat kantor/usaha Anda..." class="{{ $fieldClass }} resize-y"></textarea>
                    </div>
                    <div>
                        <label for="lead-message" class="{{ $labelClass }}">Deskripsi Kebutuhan (opsional)</label>
                        <textarea id="lead-message" name="message" rows="5" placeholder="Ceritakan kebutuhan Anda..." class="{{ $fieldClass }} resize-y"></textarea>
                    </div>
                </div>

                {{-- Step 4: Ringkasan --}}
                <div data-step="4" class="hidden space-y-8">
                    <div>
                        <h2 class="font-display font-semibold text-2xl md:text-3xl text-white tracking-tight mb-6">Ringkasan</h2>
                        <dl class="rounded-xl border border-white/10 divide-y divide-white/10 text-sm">
                            <div class="flex items-start justify-between gap-4 p-5">
                                <div class="min-w-0">
                                    <dt class="font-mono text-[10px] uppercase tracking-[0.2em] text-white/40 mb-1.5">Nama / Email / WA</dt>
                                    <dd data-summary="identity" class="text-white/85 break-words"></dd>
                                </div>
                                <button type="button" data-step-goto="1" class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-400 hover:text-brand-300 shrink-0">Edit</button>
                            </div>
                            <div class="flex items-start justify-between gap-4 p-5">
                                <div class="min-w-0">
                                    <dt class="font-mono text-[10px] uppercase tracking-[0.2em] text-white/40 mb-1.5">Layanan / Anggaran</dt>
                                    <dd data-summary="needs" class="text-white/85 break-words"></dd>
                                </div>
                                <button type="button" data-step-goto="2" class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-400 hover:text-brand-300 shrink-0">Edit</button>
                            </div>
                            <div class="flex items-start justify-between gap-4 p-5">
                                <div class="min-w-0">
                                    <dt class="font-mono text-[10px] uppercase tracking-[0.2em] text-white/40 mb-1.5">Alamat / Deskripsi</dt>
                                    <dd data-summary="details" class="text-white/85 break-words"></dd>
                                </div>
                                <button type="button" data-step-goto="3" class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-400 hover:text-brand-300 shrink-0">Edit</button>
                            </div>
                        </dl>
                    </div>

                    @if ($selectedPackage)
                        <div>
                            <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-white/45 mb-3">Paket yang kamu pilih</p>
                            <div class="relative overflow-hidden rounded-xl border border-brand-400/40 bg-brand-400/[0.05] p-6">
                                <div class="flex flex-wrap items-baseline justify-between gap-3 mb-4">
                                    <p class="font-display font-semibold text-xl text-white">{{ $selectedPackage['name'] }}</p>
                                    <p class="font-display text-xl text-brand-300 tabular-nums">
                                        {{ $selectedPackage['price'] ? \App\Support\PriceFormatter::idr($selectedPackage['price']) : $selectedPackage['price_suffix'] }}
                                    </p>
                                </div>
                                <ul class="space-y-2">
                                    @foreach (array_slice($selectedPackage['features'] ?? [], 0, 4) as $feature)
                                        <li class="flex items-start gap-3 text-sm text-white/70">
                                            <span class="w-3 h-px bg-brand-400 shrink-0 mt-[0.7rem]" aria-hidden="true"></span>
                                            {{-- package features are {label, note} (older rows: plain strings) --}}
                                            <span>{{ is_array($feature) ? ($feature['label'] ?? '') : $feature }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif
                </div>

                <p data-step-error class="text-xs text-red-400 mt-4" role="alert"></p>

                <div class="flex items-center justify-between mt-10 pt-6 border-t border-white/10">
                    <button type="button" data-step-back class="invisible font-mono text-[11px] uppercase tracking-[0.18em] text-white/55 hover:text-white px-2 py-2">&larr; Kembali</button>
                    <button type="button" data-step-next class="btn-space btn-space--lg btn-space--pill">Lanjut <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></button>
                    <button type="submit" data-step-submit class="hidden btn-space btn-space--lg btn-space--pill">Kirim <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></button>
                </div>
            </form>
        </div>
    </section>
</x-layouts.public>
