<script setup>
/**
 * Original design — no third-party template. We don't have the actual
 * Colorlib file in this project (never uploaded), so this isn't a
 * conversion of their work and carries no Colorlib attribution; adding
 * their credit to a page we didn't build from their template would be
 * inaccurate, not "safe."
 */
import { Head, Link } from "@inertiajs/vue3";
</script>

<template>
    <Head title="Halaman Tidak Ditemukan" />
    <div class="min-h-screen bg-charcoal flex items-center justify-center px-4">
        <div class="text-center max-w-md">
            <p class="text-brand-500 font-bold text-8xl md:text-9xl tracking-tight">404</p>
            <h1 class="text-white text-xl md:text-2xl font-semibold mt-4">Halaman Tidak Ditemukan</h1>
            <p class="text-gray-400 text-sm mt-3">
                Halaman yang Anda cari mungkin sudah dipindahkan, dihapus, atau alamatnya salah ketik.
            </p>
            <Link
                href="/"
                class="inline-block mt-8 bg-brand-600 text-white font-medium px-6 py-3 rounded-md hover:bg-brand-700 transition-colors"
            >
                Kembali ke Beranda
            </Link>
        </div>
    </div>
</template>
