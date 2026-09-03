<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import PageHeaderScanner from "@/Components/Public/PageHeaderScanner.vue";
import PricingSection from "@/Components/Public/PricingSection.vue";
import MaintenanceSection from "@/Components/Public/MaintenanceSection.vue";
import LiveTicker from "@/Components/Public/LiveTicker.vue";
import { Info } from "lucide-vue-next";
import { Link, usePage } from "@inertiajs/vue3";

defineProps({
    packages: Array,
    maintenancePlans: Array,
    recentActivity: Array,
});

const page = usePage();
const websiteName = page.props.settings?.general?.website_name || "Kami";
const initialCategory = new URLSearchParams(window.location.search).get("kategori") || "";
</script>

<template>
    <SeoHead
        title="Harga Paket Website"
        description="Daftar harga paket pembuatan website untuk landing page, company profile, e-commerce, dan sistem informasi."
    />
    <PublicLayout>
        <LiveTicker :activities="recentActivity" />
        <PageHeaderScanner
            eyebrow="Harga"
            :title="`Harga Paket Jasa Pembuatan Website ${websiteName}`"
            subtitle="Pilih kategori dan tier sesuai kebutuhan. Harga akhir dapat menyesuaikan kebutuhan, jumlah halaman, fitur, integrasi, dan kompleksitas project."
        />

        <PricingSection :packages="packages" :initial-category="initialCategory" />
        <MaintenanceSection :plans="maintenancePlans" />

        <div class="max-w-3xl mx-auto px-4 pb-16">
            <p v-scroll-reveal class="text-center text-sm text-gray-600 mb-4">
                Butuh scope di luar paket ini? Kami terbuka untuk project custom —
                <Link href="/konsultasi" class="text-brand-600 font-medium hover:underline">konsultasikan kebutuhan Anda</Link>.
            </p>

            <div v-scroll-reveal class="flex items-start gap-3 bg-gray-50 border border-black/10 rounded-lg p-4 md:p-5">
                <Info class="w-4 h-4 text-gray-500 shrink-0 mt-0.5" />
                <p class="text-xs text-gray-600 leading-relaxed">
                    Biaya transaksi payment gateway, kuota API pihak ketiga (ongkir, notifikasi WhatsApp), dan
                    langganan layanan eksternal ditanggung terpisah oleh klien sesuai pemakaian aktual. Setup dan
                    pengelolaan domain & hosting kami tangani penuh — Anda tidak perlu membuat akun atau mengelola
                    panel sendiri, dan domain tetap terdaftar atas nama Anda (WHOIS Anda). Domain & hosting dapat
                    diperpanjang tahunan sesuai harga berlaku saat itu.
                </p>
            </div>
        </div>
    </PublicLayout>
</template>
