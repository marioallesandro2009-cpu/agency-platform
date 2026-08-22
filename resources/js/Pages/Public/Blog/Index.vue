<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import PageHeaderScanner from "@/Components/Public/PageHeaderScanner.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { readingTimeMinutes } from "@/utils/seoAnalyzer";

const props = defineProps({
    posts: Array,
    categories: Array,
    activeCategory: Object,
});

const mascot = usePage().props.mascots?.empty_state_blog;

function formatDate(date) {
    if (!date) return "";
    return new Date(date).toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" });
}
</script>

<template>
    <SeoHead
        :title="activeCategory ? 'Kategori: ' + activeCategory.name : 'Blog & Artikel'"
        :description="activeCategory ? `Kumpulan artikel seputar ${activeCategory.name}.` : 'Artikel dan insight seputar pembuatan website, UMKM, dan bisnis online.'"
    />
    <PublicLayout>
        <PageHeaderScanner
            eyebrow="Blog"
            :title="activeCategory ? 'Kategori: ' + activeCategory.name : 'Artikel & Insight'"
            subtitle="Artikel dan insight seputar pembuatan website, UMKM, dan bisnis online."
        />

        <section class="max-w-7xl mx-auto px-4 py-20 md:py-24">
            <!-- Category filter -->
            <div class="flex flex-wrap gap-2 justify-center mb-14">
                <Link
                    href="/blog"
                    :class="!activeCategory ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                    class="text-sm px-4 py-1.5 rounded-full transition-colors"
                >
                    Semua
                </Link>
                <Link
                    v-for="cat in categories"
                    :key="cat.id"
                    :href="'/blog/category/' + cat.slug"
                    :class="activeCategory?.id === cat.id ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                    class="text-sm px-4 py-1.5 rounded-full transition-colors"
                >
                    {{ cat.name }}
                </Link>
            </div>

            <div v-if="posts.length === 0" class="text-center text-gray-500 py-12">
                <img v-if="mascot?.image_path" :src="mascot.image_path" :alt="mascot.alt_text || ''" width="128" height="128" loading="lazy" class="w-32 h-32 mx-auto mb-4 object-contain" />
                Belum ada artikel di kategori ini.
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-14">
                <article v-for="(post, index) in posts" :key="post.id" v-scroll-reveal="{ delay: Math.min(index, 6) * 80 }" class="group">
                    <Link :href="'/blog/' + post.slug" class="block">
                        <div class="aspect-[5/3] rounded-lg overflow-hidden bg-gray-100 mb-4">
                            <img
                                v-if="post.featured_image"
                                :src="post.featured_image"
                                :alt="post.title"
                                width="400"
                                height="240"
                                loading="lazy"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                        </div>
                        <p v-if="post.category" class="text-xs text-brand-600 font-semibold uppercase tracking-wide mb-2">{{ post.category.name }}</p>
                        <h2 class="font-bold text-lg tracking-tight text-gray-900 mb-2 line-clamp-2 transition-colors group-hover:text-brand-600">
                            {{ post.title }}
                        </h2>
                        <p class="text-sm text-gray-500 mb-3 line-clamp-2 leading-relaxed">{{ post.excerpt }}</p>
                        <p class="text-xs text-gray-500">{{ formatDate(post.published_at) }} - {{ readingTimeMinutes(post.content) }} menit baca</p>
                    </Link>
                </article>
            </div>
        </section>
    </PublicLayout>
</template>