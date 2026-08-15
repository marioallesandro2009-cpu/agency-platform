<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import PageHeaderScanner from "@/Components/Public/PageHeaderScanner.vue";
import { Link } from "@inertiajs/vue3";

defineProps({
    about: Object,
});
</script>

<template>
    <SeoHead
        title="Tentang Kami"
        :description="about?.description || 'Kenali lebih dekat tim dan visi misi kami dalam membangun website untuk bisnis Anda.'"
    />
    <PublicLayout>
        <PageHeaderScanner
            :eyebrow="about?.subtitle || 'Siapa Kami'"
            :title="about?.title || 'Tentang Kami'"
        />

        <section v-if="about?.description" class="max-w-7xl mx-auto px-4 py-16">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-center">
                <p v-scroll-reveal="{ direction: 'left' }" class="text-left text-gray-600 leading-relaxed text-lg">
                    {{ about.description }}
                </p>
                <div v-scroll-reveal="{ direction: 'right', delay: 100 }" class="aspect-[4/3] rounded-lg overflow-hidden bg-gray-100">
                    <img
                        :src="about.image || 'https://placehold.co/600x450'"
                        :alt="about.image ? (about.title || 'Tentang Kami') : ''"
                        :aria-hidden="!about.image"
                        class="w-full h-full object-cover"
                    />
                </div>
            </div>
        </section>

        <section v-if="about?.mission || about?.vision" class="bg-gray-50 py-16">
            <div class="max-w-5xl mx-auto px-4 grid grid-cols-1 md:grid-cols-2 gap-8">
                <div
                    v-if="about.mission"
                    v-scroll-reveal="{ direction: 'left' }"
                    class="bg-white border border-black/10 rounded-xl p-6 transition-all duration-300 hover:-translate-y-1 hover:border-brand-300 hover:shadow-lg"
                >
                    <h2 class="font-semibold text-gray-900 mb-2">Misi</h2>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ about.mission }}</p>
                </div>
                <div
                    v-if="about.vision"
                    v-scroll-reveal="{ direction: 'right' }"
                    class="bg-white border border-black/10 rounded-xl p-6 transition-all duration-300 hover:-translate-y-1 hover:border-brand-300 hover:shadow-lg"
                >
                    <h2 class="font-semibold text-gray-900 mb-2">Visi</h2>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ about.vision }}</p>
                </div>
            </div>
        </section>

        <section v-if="about?.values?.length" class="max-w-7xl mx-auto px-4 py-16">
            <h2 v-scroll-reveal class="text-2xl font-bold text-gray-900 text-center mb-10">Nilai-Nilai Kami</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div v-for="(value, index) in about.values" :key="value.title" v-scroll-reveal="{ delay: index * 80 }" class="text-center">
                    <h3 class="font-semibold text-gray-900 mb-2">{{ value.title }}</h3>
                    <p class="text-sm text-gray-500">{{ value.description }}</p>
                </div>
            </div>
        </section>

        <section v-if="about?.technologies?.length" class="bg-gray-50 py-16">
            <div v-scroll-reveal class="max-w-3xl mx-auto px-4 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Teknologi yang Kami Gunakan</h2>
                <div class="flex flex-wrap justify-center gap-2">
                    <span
                        v-for="tech in about.technologies"
                        :key="tech"
                        class="bg-white border border-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-full"
                    >
                        {{ tech }}
                    </span>
                </div>
            </div>
        </section>

        <section class="bg-brand-600 py-16">
            <div v-scroll-reveal class="max-w-2xl mx-auto px-4 text-center">
                <h2 class="text-2xl font-bold text-white mb-4">Mari Bekerja Sama</h2>
                <p class="text-white/80 mb-6">Konsultasikan kebutuhan website Anda dengan tim kami.</p>
                <Link
                    href="/konsultasi"
                    class="inline-block bg-white text-brand-700 font-medium px-8 py-3 rounded-md transition-all duration-300 hover:bg-brand-50 hover:scale-105 hover:shadow-lg"
                >
                    Konsultasi Gratis
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>