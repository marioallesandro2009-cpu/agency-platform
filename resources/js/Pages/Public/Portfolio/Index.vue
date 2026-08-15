<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import PageHeaderScanner from "@/Components/Public/PageHeaderScanner.vue";
import { Link } from "@inertiajs/vue3";

defineProps({
    projects: Array,
});

function domainLabel(url) {
    if (!url) return null;
    try {
        return new URL(url).hostname.replace(/^www\./, "");
    } catch (e) {
        return url;
    }
}
</script>

<template>
    <SeoHead
        title="Portfolio"
        description="Kumpulan proyek website yang sudah kami kerjakan untuk berbagai bisnis dan UMKM."
    />
    <PublicLayout>
        <PageHeaderScanner
            eyebrow="Portfolio"
            title="Hasil Karya Kami"
            subtitle="Beberapa proyek konsep yang menunjukkan pendekatan kami dalam membangun website untuk berbagai kebutuhan bisnis."
        />

        <section class="max-w-6xl mx-auto px-4 py-16">
            <div v-if="projects.length === 0" class="text-center text-gray-500 py-12">
                Belum ada proyek portfolio yang dipublikasikan.
            </div>

            <div v-else class="divide-y divide-gray-200">
                <div
                    v-for="project in projects"
                    :key="project.id"
                    class="py-12 first:pt-0 grid grid-cols-1 md:grid-cols-2 gap-10 items-center"
                >
                    <div v-scroll-reveal="{ direction: 'left' }">
                        <Link :href="'/portfolio/' + project.slug" class="block">
                            <span
                                v-if="project.project_type === 'personal'"
                                class="inline-block bg-gray-100 text-gray-700 text-xs font-medium px-3 py-1 rounded-full mb-3"
                            >
                                Personal Project
                            </span>
                            <h2 class="text-2xl font-semibold text-gray-800 mb-4 hover:text-brand-600 transition-colors">
                                {{ project.name }}
                            </h2>
                        </Link>
                        <p class="text-gray-500 leading-relaxed mb-6">
                            {{ project.short_description }}
                        </p>
                            <a
                            v-if="project.live_url"
                            :href="project.live_url"
                            target="_blank"
                            class="text-gray-500 hover:text-brand-600 text-sm"
                        >
                            {{ domainLabel(project.live_url) }}
                        </a>
                        <Link
                            v-else
                            :href="'/portfolio/' + project.slug"
                            class="text-brand-600 hover:underline text-sm"
                        >
                            Lihat detail proyek
                        </Link>
                    </div>

                    <Link v-scroll-reveal="{ direction: 'right' }" :href="'/portfolio/' + project.slug" class="block">
                        <div class="rounded-lg overflow-hidden shadow-lg">
                            <img
                                v-if="project.thumbnail"
                                :src="project.thumbnail"
                                :alt="project.name"
                                class="w-full h-auto object-cover"
                            />
                            <div v-else class="w-full h-56 bg-gray-100"></div>
                        </div>
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>