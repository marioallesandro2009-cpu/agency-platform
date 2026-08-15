<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import PageHeaderScanner from "@/Components/Public/PageHeaderScanner.vue";
import { Link } from "@inertiajs/vue3";

defineProps({
    services: Array,
    portfolioProjects: Array,
    pricingPackages: Array,
});

function formatPrice(price) {
    if (!price) return null;
    return "Rp " + new Intl.NumberFormat("id-ID").format(price);
}
</script>

<template>
    <SeoHead
        title="Jasa Website"
        description="Kami membangun website yang cepat, modern, dan sesuai kebutuhan bisnis Anda — dari UMKM hingga sistem custom."
    />
    <PublicLayout>
        <PageHeaderScanner
            eyebrow="Jasa Website"
            title="Jasa Website Profesional"
            subtitle="Kami membangun website yang cepat, modern, dan sesuai kebutuhan bisnis Anda — dari UMKM hingga sistem custom."
        >
            <Link href="/konsultasi" class="inline-block bg-brand-600 hover:bg-brand-700 text-white font-medium px-6 py-3 rounded-md">
                Konsultasi Gratis
            </Link>
        </PageHeaderScanner>

        <!-- Service types -->
        <section class="max-w-7xl mx-auto px-4 py-16">
            <h2 v-scroll-reveal class="text-2xl font-bold text-gray-900 text-center mb-10">Jenis Layanan Website Kami</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <Link
                    v-for="(service, index) in services"
                    :key="service.id"
                    :href="'/' + service.slug"
                    v-scroll-reveal="{ delay: index * 80 }"
                    class="block border border-black/10 rounded-xl p-6 transition-all duration-300 hover:-translate-y-1 hover:border-brand-300 hover:shadow-lg"
                >
                    <h3 class="font-semibold text-lg text-gray-900 mb-2">{{ service.name }}</h3>
                    <p class="text-sm text-gray-500 mb-4">{{ service.short_description }}</p>
                    <span class="text-brand-600 text-sm font-medium">Pelajari lebih lanjut →</span>
                </Link>
            </div>
        </section>

        <!-- Why website matters -->
        <section class="bg-gray-50 py-16">
            <div class="max-w-4xl mx-auto px-4">
                <h2 v-scroll-reveal class="text-2xl font-bold text-gray-900 text-center mb-10">Kenapa Bisnis Anda Butuh Website</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div v-scroll-reveal class="text-center">
                        <h3 class="font-semibold text-gray-900 mb-2">Kredibilitas</h3>
                        <p class="text-sm text-gray-500">Website profesional membangun kepercayaan calon pelanggan lebih cepat dibanding media sosial saja.</p>
                    </div>
                    <div v-scroll-reveal="{ delay: 80 }" class="text-center">
                        <h3 class="font-semibold text-gray-900 mb-2">Jangkauan Lebih Luas</h3>
                        <p class="text-sm text-gray-500">Ditemukan lewat pencarian Google, bukan cuma dari orang yang sudah follow akun Anda.</p>
                    </div>
                    <div v-scroll-reveal="{ delay: 160 }" class="text-center">
                        <h3 class="font-semibold text-gray-900 mb-2">Kendali Penuh</h3>
                        <p class="text-sm text-gray-500">Konten, tampilan, dan data pelanggan sepenuhnya milik Anda, tidak terikat platform pihak ketiga.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Portfolio -->
        <section v-if="portfolioProjects.length" class="max-w-7xl mx-auto px-4 py-16">
            <h2 v-scroll-reveal class="text-2xl font-bold text-gray-900 text-center mb-10">Contoh Hasil Kerja Kami</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <Link
                    v-for="(project, index) in portfolioProjects"
                    :key="project.id"
                    :href="'/portfolio/' + project.slug"
                    v-scroll-reveal="{ delay: index * 80 }"
                    class="group block bg-white border border-black/10 rounded-xl overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:border-brand-300 hover:shadow-lg"
                >
                    <div class="h-40 bg-gray-100">
                        <img
                            v-if="project.thumbnail"
                            :src="project.thumbnail"
                            :alt="project.name"
                            class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                        />
                    </div>
                    <div class="p-4">
                        <p class="text-xs text-brand-600 font-medium mb-1">{{ project.category }}</p>
                        <h3 class="font-semibold text-gray-900 text-sm">{{ project.name }}</h3>
                    </div>
                </Link>
            </div>
            <div class="text-center mt-8">
                <Link href="/portfolio" class="text-brand-600 font-medium hover:underline">Lihat semua portfolio →</Link>
            </div>
        </section>

        <!-- Pricing teaser -->
        <section v-if="pricingPackages.length" class="bg-gray-50 py-16">
            <div class="max-w-7xl mx-auto px-4">
                <h2 v-scroll-reveal class="text-2xl font-bold text-gray-900 text-center mb-10">Estimasi Harga</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                    <div
                        v-for="(pkg, index) in pricingPackages"
                        :key="pkg.id"
                        v-scroll-reveal="{ delay: index * 80 }"
                        class="bg-white border border-black/10 rounded-xl p-5 transition-all duration-300 hover:-translate-y-1 hover:border-brand-300 hover:shadow-lg"
                    >
                        <h3 class="font-semibold text-gray-900 mb-1">{{ pkg.name }}</h3>
                        <p v-if="pkg.price_prefix" class="text-xs text-gray-500">{{ pkg.price_prefix }}</p>
                        <p v-if="pkg.price" class="text-lg font-bold text-gray-900">{{ formatPrice(pkg.price) }}</p>
                        <p v-else-if="pkg.price_suffix" class="text-lg font-bold text-gray-900">{{ pkg.price_suffix }}</p>
                    </div>
                </div>
                <div class="text-center mt-8">
                    <Link href="/harga" class="text-brand-600 font-medium hover:underline">Lihat detail paket harga →</Link>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="bg-brand-600 py-16">
            <div v-scroll-reveal class="max-w-2xl mx-auto px-4 text-center">
                <h2 class="text-2xl font-bold text-white mb-4">Siap Membangun Website Anda?</h2>
                <p class="text-white/80 mb-6">Konsultasikan kebutuhan Anda secara gratis, tanpa komitmen.</p>
                <Link href="/konsultasi" class="inline-block bg-white text-brand-700 font-medium px-8 py-3 rounded-md hover:bg-brand-50">
                    Konsultasi Gratis
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>