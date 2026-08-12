<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import PageHeaderScanner from "@/Components/Public/PageHeaderScanner.vue";
import PricingSection from "@/Components/Public/PricingSection.vue";
import MaintenanceSection from "@/Components/Public/MaintenanceSection.vue";
import { usePage } from "@inertiajs/vue3";

defineProps({
    packages: Array,
    maintenancePlans: Array,
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
        <PageHeaderScanner
            eyebrow="Harga"
            :title="`Harga Paket Jasa Pembuatan Website ${websiteName}`"
            subtitle="Pilih kategori dan tier sesuai kebutuhan. Harga akhir dapat menyesuaikan kebutuhan, jumlah halaman, fitur, integrasi, dan kompleksitas project."
        />

        <PricingSection :packages="packages" :initial-category="initialCategory" />
        <MaintenanceSection :plans="maintenancePlans" />
    </PublicLayout>
</template>
