<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import PageHeaderScanner from "@/Components/Public/PageHeaderScanner.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    service: Object,
    otherServices: Array,
    relatedPosts: Array,
    relatedPortfolio: Array,
});

const page = usePage();

const jsonLd = computed(() => ({
    "@context": "https://schema.org",
    "@type": "Service",
    "name": props.service.name,
    "description": props.service.seo_description || props.service.short_description,
    "provider": {
        "@type": "Organization",
        "name": page.props.settings?.general?.website_name,
    },
}));

function formatDate(date) {
    if (!date) return "";
    return new Date(date).toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" });
}

const breadcrumbItems = computed(() => [
    { label: "Home", href: "/" },
    { label: "Jasa & Layanan", href: "/jasa-website" },
    { label: props.service.name },
]);
</script>

<template>
    <SeoHead
        :title="service.seo_title || service.name"
        :description="service.seo_description || service.short_description"
        :json-ld="jsonLd"
    />
    <PublicLayout>
        <PageHeaderScanner
            eyebrow="Jasa & Layanan"
            :title="service.name"
            :subtitle="service.short_description"
            :breadcrumb-items="breadcrumbItems"
        />
        <div class="max-w-7xl mx-auto px-4 py-16 grid grid-cols-1 lg:grid-cols-3 gap-10">
            <!-- Main content -->
            <div v-scroll-reveal class="lg:col-span-2">
                <div class="prose max-w-none text-lg text-gray-700 leading-relaxed whitespace-pre-line" v-html="service.long_description || service.description"></div>

                <div v-if="service.features?.length" class="mt-10">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Fitur</h2>
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-base text-gray-600">
                        <li v-for="f in service.features" :key="f">- {{ f }}</li>
                    </ul>
                </div>

                <div v-if="service.benefits?.length" class="mt-10">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Manfaat</h2>
                    <ul class="space-y-3 text-base text-gray-600">
                        <li v-for="b in service.benefits" :key="b">- {{ b }}</li>
                    </ul>
                </div>

                <div class="mt-12 pt-8 border-t">
                    <Link :href="service.cta_url || '/konsultasi'" class="inline-block bg-brand-600 text-white font-medium px-6 py-3 rounded-md hover:bg-brand-700">
                        {{ service.cta_text || "Konsultasi Gratis Sekarang" }}
                    </Link>
                </div>
            </div>

            <!-- Sidebar -->
            <aside v-scroll-reveal="{ direction: 'right' }" class="space-y-8">
                <!-- Related articles -->
                <div v-if="relatedPosts?.length">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Terkait</h3>
                    <div class="space-y-4">
                        <Link
                            v-for="post in relatedPosts"
                            :key="post.id"
                            :href="'/blog/' + post.slug"
                            class="block group"
                        >
                            <p class="text-sm font-medium text-gray-800 group-hover:text-brand-600 line-clamp-2">{{ post.title }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ formatDate(post.published_at) }}</p>
                        </Link>
                    </div>
                </div>

                <!-- Portfolio (toggle-controlled) -->
                <div v-if="relatedPortfolio?.length">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Portfolio Terkait</h3>
                    <div class="space-y-3">
                        <Link
                            v-for="project in relatedPortfolio"
                            :key="project.id"
                            :href="'/portfolio/' + project.slug"
                            class="flex items-center gap-3 group"
                        >
                            <div class="w-14 h-14 rounded-md bg-gray-100 overflow-hidden flex-shrink-0">
                                <img v-if="project.thumbnail" :src="project.thumbnail" :alt="project.name" class="w-full h-full object-cover" />
                            </div>
                            <span class="text-sm text-gray-700 group-hover:text-brand-600">{{ project.name }}</span>
                        </Link>
                    </div>
                </div>

                <!-- Other services -->
                <div v-if="otherServices?.length">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Layanan Lainnya</h3>
                    <div class="space-y-3">
                        <Link
                            v-for="s in otherServices"
                            :key="s.id"
                            :href="'/' + s.slug"
                            class="block text-sm text-gray-700 hover:text-brand-600"
                        >
                            {{ s.name }}
                        </Link>
                    </div>
                </div>
            </aside>
        </div>
    </PublicLayout>
</template>