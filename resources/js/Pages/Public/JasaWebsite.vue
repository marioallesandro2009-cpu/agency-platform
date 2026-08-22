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
            <Link href="/konsultasi" class="inline-block bg-brand-600 text-white text-sm font-semibold uppercase tracking-wide px-7 py-3.5 rounded-md transition-all duration-200 hover:bg-brand-700 hover:-translate-y-0.5">
                Konsultasi Gratis
            </Link>
        </PageHeaderScanner>

        <!-- Service types -->
        <section class="max-w-5xl mx-auto px-4 py-20">
            <h2 v-scroll-reveal class="text-3xl md:text-4xl font-bold tracking-tight text-gray-900 text-center mb-14">Jenis Layanan Website Kami</h2>
            <div class="border-t border-gray-200">
                <Link
                    v-for="(service, index) in services"
                    :key="service.id"
                    :href="'/' + service.slug"
                    v-scroll-reveal="{ delay: index * 80 }"
                    class="group flex items-center justify-between gap-6 border-b border-gray-200 py-6"
                >
                    <div class="flex items-baseline gap-5">
                        <span class="text-sm font-semibold text-gray-400 tabular-nums">{{ String(index + 1).padStart(2, "0") }}</span>
                        <div>
                            <h3 class="font-bold text-lg text-gray-900 tracking-tight transition-colors group-hover:text-brand-600">{{ service.name }}</h3>
                            <p class="text-sm text-gray-500 mt-1 max-w-md">{{ service.short_description }}</p>
                        </div>
                    </div>
                    <span aria-hidden="true" class="text-brand-600 flex-shrink-0 transition-transform duration-200 group-hover:translate-x-1">-&gt;</span>
                </Link>
            </div>
        </section>

        <!-- Why website matters -->
        <section class="bg-gray-50 py-20">
            <div class="max-w-4xl mx-auto px-4">
                <h2 v-scroll-reveal class="text-3xl md:text-4xl font-bold tracking-tight text-gray-900 text-center mb-14">Kenapa Bisnis Anda Butuh Website</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    <div v-scroll-reveal class="text-center">
                        <h3 class="font-bold tracking-tight text-gray-900 mb-2">Kredibilitas</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Website profesional membangun kepercayaan calon pelanggan lebih cepat dibanding media sosial saja.</p>
                    </div>
                    <div v-scroll-reveal="{ delay: 80 }" class="text-center">
                        <h3 class="font-bold tracking-tight text-gray-900 mb-2">Jangkauan Lebih Luas</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Ditemukan lewat pencarian Google, bukan cuma dari orang yang sudah follow akun Anda.</p>
                    </div>
                    <div v-scroll-reveal="{ delay: 160 }" class="text-center">
                        <h3 class="font-bold tracking-tight text-gray-900 mb-2">Kendali Penuh</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">Konten, tampilan, dan data pelanggan sepenuhnya milik Anda, tidak terikat platform pihak ketiga.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Portfolio -->
        <section v-if="portfolioProjects.length" class="max-w-7xl mx-auto px-4 py-20">
            <h2 v-scroll-reveal class="text-3xl md:text-4xl font-bold tracking-tight text-gray-900 text-center mb-14">Contoh Hasil Kerja Kami</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <Link
                    v-for="(project, index) in portfolioProjects"
                    :key="project.id"
                    :href="'/portfolio/' + project.slug"
                    v-scroll-reveal="{ delay: index * 80 }"
                    class="group block"
                >
                    <div class="aspect-[4/3] rounded-lg overflow-hidden bg-gray-100 mb-4">
                        <img
                            v-if="project.thumbnail"
                            :src="project.thumbnail"
                            :alt="project.name"
                            width="400"
                            height="300"
                            loading="lazy"
                            class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                        />
                    </div>
                    <p class="text-xs text-brand-600 font-semibold uppercase tracking-wide mb-1">{{ project.category }}</p>
                    <h3 class="font-bold text-gray-900 tracking-tight transition-colors group-hover:text-brand-600">{{ project.name }}</h3>
                </Link>
            </div>
            <div class="text-center mt-12">
                <Link href="/portfolio" class="text-brand-600 font-semibold text-sm hover:underline">Lihat semua portfolio →</Link>
            </div>
        </section>

        <!-- Pricing teaser -->
        <section v-if="pricingPackages.length" class="bg-gray-50 py-20">
            <div class="max-w-7xl mx-auto px-4">
                <h2 v-scroll-reveal class="text-3xl md:text-4xl font-bold tracking-tight text-gray-900 text-center mb-14">Estimasi Harga</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                    <div
                        v-for="(pkg, index) in pricingPackages"
                        :key="pkg.id"
                        v-scroll-reveal="{ delay: index * 80 }"
                        class="bg-white rounded-lg border border-black/10 p-5 transition-colors duration-200 hover:border-brand-300"
                    >
                        <h3 class="font-bold tracking-tight text-gray-900 mb-1">{{ pkg.name }}</h3>
                        <p v-if="pkg.price_prefix" class="text-xs text-gray-500">{{ pkg.price_prefix }}</p>
                        <p v-if="pkg.price" class="text-lg font-bold text-gray-900">{{ formatPrice(pkg.price) }}</p>
                        <p v-else-if="pkg.price_suffix" class="text-lg font-bold text-gray-900">{{ pkg.price_suffix }}</p>
                    </div>
                </div>
                <div class="text-center mt-12">
                    <Link href="/harga" class="text-brand-600 font-semibold text-sm hover:underline">Lihat detail paket harga →</Link>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="bg-brand-600 py-20">
            <div v-scroll-reveal class="max-w-2xl mx-auto px-4 text-center">
                <h2 class="text-3xl font-bold tracking-tight text-white mb-4">Siap Membangun Website Anda?</h2>
                <p class="text-white/80 mb-8">Konsultasikan kebutuhan Anda secara gratis, tanpa komitmen.</p>
                <Link href="/konsultasi" class="inline-block bg-white text-brand-700 text-sm font-semibold uppercase tracking-wide px-8 py-3.5 rounded-md transition-all duration-200 hover:bg-brand-50 hover:-translate-y-0.5">
                    Konsultasi Gratis
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>