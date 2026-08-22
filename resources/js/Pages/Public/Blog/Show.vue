<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import PageHeaderScanner from "@/Components/Public/PageHeaderScanner.vue";
import Button from "@/Components/Public/Button.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";
import { readingTimeMinutes } from "@/utils/seoAnalyzer";

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

const readingTime = computed(() => readingTimeMinutes(props.post.content));

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
        <PageHeaderScanner
            eyebrow="Blog"
            :title="post.title"
            :subtitle="post.excerpt"
            :breadcrumb-items="breadcrumbItems"
        >
            <Link
                v-if="post.category"
                :href="'/blog/category/' + post.category.slug"
                class="inline-block text-brand-400 text-xs font-semibold uppercase tracking-widest hover:text-brand-300 mb-2"
            >
                {{ post.category.name }}
            </Link>
            <div class="flex items-center gap-3 text-sm text-gray-300">
                <span v-if="post.author">{{ post.author.name }}</span>
                <span v-if="post.published_at">- {{ formatDate(post.published_at) }}</span>
                <span>- {{ readingTime }} menit baca</span>
            </div>
        </PageHeaderScanner>

        <article class="max-w-3xl mx-auto px-4 py-16 md:py-20">
            <img
                v-if="post.featured_image"
                v-scroll-reveal="{ delay: 100 }"
                :src="post.featured_image"
                :alt="post.title"
                width="1200"
                height="675"
                class="w-full rounded-lg mb-8 object-cover max-h-96"
            />

            <div class="prose max-w-none text-gray-700 whitespace-pre-line" v-html="post.content"></div>

            <div class="mt-12 pt-8 border-t border-gray-200">
                <Button href="/konsultasi">Konsultasi Gratis Sekarang</Button>
            </div>

            <div v-if="related?.length" class="mt-16">
                <h2 class="text-xl font-bold tracking-tight text-gray-900 mb-6">Artikel Terkait</h2>
                <div class="border-t border-gray-200">
                    <article v-for="(r, index) in related" :key="r.id" v-scroll-reveal="{ delay: index * 80 }" class="group border-b border-gray-200 py-5">
                        <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2">
                            <Link :href="'/blog/' + r.slug" class="transition-colors group-hover:text-brand-600">{{ r.title }}</Link>
                        </h3>
                        <p class="text-xs text-gray-500">{{ formatDate(r.published_at) }}</p>
                    </article>
                </div>
            </div>
        </article>
    </PublicLayout>
</template>