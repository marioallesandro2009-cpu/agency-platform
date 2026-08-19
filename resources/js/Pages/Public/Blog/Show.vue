<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import Breadcrumbs from "@/Components/Public/Breadcrumbs.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    post: Object,
    related: Array,
});

const page = usePage();

const jsonLd = computed(() => ({
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    "headline": props.post.title,
    "description": props.post.seo_description || props.post.excerpt,
    "image": props.post.featured_image || undefined,
    "datePublished": props.post.published_at,
    "dateModified": props.post.updated_at,
    "author": {
        "@type": "Person",
        "name": props.post.author?.name || page.props.settings?.general?.website_name,
    },
}));

function formatDate(date) {
    if (!date) return "";
    return new Date(date).toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" });
}

const breadcrumbItems = computed(() => [
    { label: "Home", href: "/" },
    { label: "Blog", href: "/blog" },
    { label: props.post.title },
]);
</script>

<template>
    <SeoHead
        :title="post.seo_title || post.title"
        :description="post.seo_description || post.excerpt"
        :image="post.featured_image"
        type="article"
        :json-ld="jsonLd"
    />
    <PublicLayout>
        <Breadcrumbs :items="breadcrumbItems" />
        <article class="max-w-3xl mx-auto px-4 py-16">
            <div v-scroll-reveal class="mb-8">
                <Link v-if="post.category" :href="'/blog/category/' + post.category.slug" class="text-brand-600 text-sm font-medium">
                    {{ post.category.name }}
                </Link>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mt-2 mb-3">{{ post.title }}</h1>
                <div class="flex items-center gap-3 text-sm text-gray-500">
                    <span v-if="post.author">{{ post.author.name }}</span>
                    <span v-if="post.published_at">- {{ formatDate(post.published_at) }}</span>
                </div>
            </div>

            <img
                v-if="post.featured_image"
                v-scroll-reveal="{ delay: 100 }"
                :src="post.featured_image"
                :alt="post.title"
                width="1200"
                height="675"
                class="w-full rounded-xl mb-8 object-cover max-h-96"
            />

            <div class="prose max-w-none text-gray-700 whitespace-pre-line" v-html="post.content"></div>

            <div class="mt-12 pt-8 border-t">
                <Link href="/konsultasi" class="inline-block bg-brand-600 text-white font-medium px-6 py-3 rounded-md hover:bg-brand-700">
                    Konsultasi Gratis Sekarang
                </Link>
            </div>

            <div v-if="related?.length" class="mt-16">
                <h2 class="text-xl font-semibold text-gray-900 mb-6">Artikel Terkait</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <article
                        v-for="(r, index) in related"
                        :key="r.id"
                        v-scroll-reveal="{ delay: index * 80 }"
                        class="border border-black/10 rounded-lg p-4 transition-all duration-300 hover:-translate-y-1 hover:border-brand-300 hover:shadow-lg"
                    >
                        <h3 class="font-medium text-gray-900 mb-2 line-clamp-2">
                            <Link :href="'/blog/' + r.slug" class="hover:text-brand-600">{{ r.title }}</Link>
                        </h3>
                        <p class="text-xs text-gray-500">{{ formatDate(r.published_at) }}</p>
                    </article>
                </div>
            </div>
        </article>
    </PublicLayout>
</template>