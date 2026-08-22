<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import Breadcrumbs from "@/Components/Public/Breadcrumbs.vue";
import PageHeaderScanner from "@/Components/Public/PageHeaderScanner.vue";
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    project: Object,
    related: Array,
});

const breadcrumbItems = computed(() => [
    { label: "Home", href: "/" },
    { label: "Portfolio", href: "/portfolio" },
    { label: props.project.name },
]);
</script>

<template>
    <SeoHead
        :title="project.seo_title || project.name"
        :description="project.seo_description || project.short_description"
        :image="project.thumbnail"
    />
    <PublicLayout>
        <Breadcrumbs :items="breadcrumbItems" />
        <PageHeaderScanner
            :eyebrow="project.category"
            :title="project.name"
            :subtitle="project.short_description"
        >
            <span
                v-if="project.project_type === 'personal'"
                class="inline-block bg-gray-100 text-gray-700 text-xs font-medium px-3 py-1 rounded-full mb-2"
            >
                Personal Project
            </span>
            <p v-if="project.client_label" class="text-xs text-gray-400 italic">{{ project.client_label }}</p>
        </PageHeaderScanner>

        <div class="max-w-4xl mx-auto px-4">
            <div v-scroll-reveal="{ delay: 100 }" class="relative h-64 md:h-96 bg-gray-100 -mt-8 rounded-xl overflow-hidden shadow-xl">
                <img
                    v-if="project.cover_image"
                    :src="project.cover_image"
                    :alt="project.name"
                    width="896"
                    height="384"
                    class="w-full h-full object-cover"
                />
            </div>
        </div>

        <section class="max-w-4xl mx-auto px-4 py-16 space-y-12">
            <div v-if="project.description">
                <h2 class="text-xl font-semibold text-gray-900 mb-3">Tentang Proyek</h2>
                <p class="text-gray-600 leading-relaxed">{{ project.description }}</p>
            </div>

            <div v-if="project.challenge" class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Tantangan</h2>
                    <p class="text-gray-600 leading-relaxed">{{ project.challenge }}</p>
                </div>
                <div v-if="project.solution">
                    <h2 class="text-xl font-semibold text-gray-900 mb-3">Solusi</h2>
                    <p class="text-gray-600 leading-relaxed">{{ project.solution }}</p>
                </div>
            </div>

            <div v-if="project.features?.length">
                <h2 class="text-xl font-semibold text-gray-900 mb-3">Fitur</h2>
                <ul class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-600">
                    <li v-for="feature in project.features" :key="feature">- {{ feature }}</li>
                </ul>
            </div>

            <div v-if="project.technologies?.length">
                <h2 class="text-xl font-semibold text-gray-900 mb-3">Teknologi</h2>
                <div class="flex flex-wrap gap-2">
                    <span
                        v-for="tech in project.technologies"
                        :key="tech"
                        class="bg-gray-100 text-gray-700 text-xs font-medium px-3 py-1 rounded-full"
                    >
                        {{ tech }}
                    </span>
                </div>
            </div>

            <div v-if="project.live_url">
                <a
                    :href="project.live_url"
                    target="_blank"
                    rel="noopener"
                    class="inline-block bg-brand-600 text-white font-medium px-6 py-3 rounded-md hover:bg-brand-700"
                >
                    Kunjungi
                </a>
            </div>

            <div class="bg-gray-50 rounded-xl p-8 text-center">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Tertarik dengan hasil serupa?</h2>
                <p class="text-gray-500 mb-4">Konsultasikan kebutuhan website Anda secara gratis.</p>
                <Link href="/konsultasi" class="inline-block bg-brand-600 text-white font-medium px-6 py-3 rounded-md hover:bg-brand-700">
                    Konsultasi Gratis
                </Link>
            </div>

            <div v-if="related.length">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Proyek Terkait</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Link
                        v-for="(item, index) in related"
                        :key="item.id"
                        :href="'/portfolio/' + item.slug"
                        v-scroll-reveal="{ delay: index * 80 }"
                        class="group block bg-white border border-black/10 rounded-xl overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:border-brand-300 hover:shadow-lg"
                    >
                        <div class="h-32 bg-gray-100">
                            <img
                                v-if="item.thumbnail"
                                :src="item.thumbnail"
                                :alt="item.name"
                                width="400"
                                height="128"
                                loading="lazy"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform"
                            />
                        </div>
                        <div class="p-4">
                            <p class="text-xs text-brand-600 font-medium mb-1">{{ item.category }}</p>
                            <h3 class="font-semibold text-gray-900 text-sm">{{ item.name }}</h3>
                        </div>
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>