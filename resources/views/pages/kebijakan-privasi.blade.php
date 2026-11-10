@php
    $email = $settings['contact']['email'] ?? null;
    $sections = [
        ['title' => 'Data yang Anda berikan', 'text' => 'Saat mengisi formulir, kami menerima nama perusahaan, nama dan jabatan, nomor WhatsApp, email, alamat, anggaran, dan pesan yang Anda tulis. Data ini kami gunakan untuk menghubungi Anda dan menyiapkan penawaran.'],
        ['title' => 'Data yang tercatat otomatis', 'text' => 'Kami mencatat halaman yang dikunjungi, jenis perangkat, dan sumber kunjungan, termasuk iklan atau tautan yang membawa Anda ke situs ini. Sumber kunjungan ikut tersimpan bersama formulir agar kami tahu kanal mana yang berguna.'],
        ['title' => 'Cookie dan alat pihak ketiga', 'text' => 'Situs ini dapat memakai alat analitik dan piksel iklan dari penyedia pihak ketiga, termasuk Google dan Meta. Alat tersebut dapat memakai cookie untuk mengukur kunjungan, mengetahui apakah formulir terkirim, dan menampilkan iklan yang relevan. Anda dapat membatasinya melalui pengaturan browser dan pengaturan preferensi iklan di akun Anda.'],
        ['title' => 'Berbagi data', 'text' => 'Kami tidak menjual data Anda. Data hanya dibagikan kepada penyedia layanan yang membantu menjalankan situs dan komunikasi kami, seperti hosting dan email, atau bila diwajibkan oleh ketentuan yang berlaku.'],
        ['title' => 'Penyimpanan dan hak Anda', 'text' => 'Data disimpan selama diperlukan untuk tujuan di atas. Anda dapat meminta kami menunjukkan, memperbaiki, atau menghapus data Anda dengan menghubungi kami.'],
    ];
@endphp

<x-layouts.public theme="space" seo-title="Kebijakan Privasi" seo-description="Cara kami mengumpulkan, memakai, dan menjaga data pengunjung serta calon klien.">
    <x-public.space-page-header eyebrow="Privasi" code="PRV" title="Kebijakan Privasi" subtitle="Berlaku sejak 10 November 2026." />

    <section class="max-w-3xl mx-auto px-4 md:px-8 py-16 md:py-20 space-y-10">
        @foreach ($sections as $section)
            <div>
                <h2 class="font-display font-semibold text-2xl text-white mb-3">{{ $section['title'] }}</h2>
                <p class="text-white/70 leading-relaxed">{{ $section['text'] }}</p>
            </div>
        @endforeach
        @if ($email)
            <p class="text-white/70 leading-relaxed">Pertanyaan tentang privasi dapat dikirim ke <a href="mailto:{{ $email }}" class="text-brand-300 underline underline-offset-4">{{ $email }}</a>.</p>
        @endif
    </section>
</x-layouts.public>
