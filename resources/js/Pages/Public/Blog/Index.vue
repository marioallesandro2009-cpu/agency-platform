<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    posts: Array,
    categories: Array,
    activeCategory: Object,
});

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
        <section class="max-w-7xl mx-auto px-4 py-16">
            <div v-scroll-reveal class="text-center max-w-2xl mx-auto mb-12">
                <p class="text-brand-600 font-semibold text-sm uppercase tracking-wide mb-2">Blog</p>
                <h1 class="text-3xl font-bold text-gray-900">
                    {{ activeCategory ? "Kategori: " + activeCategory.name : "Artikel & Insight" }}
                </h1>
            </div>

            <!-- Category filter -->
            <div class="flex flex-wrap gap-2 justify-center mb-12">
                <Link
                    href="/blog"
                    :class="!activeCategory ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                    class="text-sm px-4 py-1.5 rounded-full"
                >
                    Semua
                </Link>
                <Link
                    v-for="cat in categories"
                    :key="cat.id"
                    :href="'/blog/category/' + cat.slug"
                    :class="activeCategory?.id === cat.id ? 'bg-brand-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                    class="text-sm px-4 py-1.5 rounded-full"
                >
                    {{ cat.name }}
                </Link>
            </div>

            <div v-if="posts.length === 0" class="text-center text-gray-500 py-12">
                Belum ada artikel di kategori ini.
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <article
                    v-for="(post, index) in posts"
                    :key="post.id"
                    v-scroll-reveal="{ delay: Math.min(index, 6) * 80 }"
                    class="border border-black/10 rounded-xl overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:border-brand-300 hover:shadow-lg"
                >
                    <div class="h-48 bg-gray-100">
                        <img v-if="post.featured_image" :src="post.featured_image" :alt="post.title" class="w-full h-full object-cover" />
                    </div>
                    <div class="p-5">
                        <p v-if="post.category" class="text-xs text-brand-600 font-medium mb-2">{{ post.category.name }}</p>
                        <h2 class="font-semibold text-lg text-gray-900 mb-2 line-clamp-2">
                            <Link :href="'/blog/' + post.slug" class="hover:text-brand-600">{{ post.title }}</Link>
                        </h2>
                        <p class="text-sm text-gray-500 mb-3 line-clamp-2">{{ post.excerpt }}</p>
                        <p class="text-xs text-gray-500">{{ formatDate(post.published_at) }}</p>
                    </div>
                </article>
            </div>
        </section>
    </PublicLayout>
</template>