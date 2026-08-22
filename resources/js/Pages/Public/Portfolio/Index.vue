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

        <section class="max-w-6xl mx-auto px-4 py-16 md:py-20">
            <div v-if="projects.length === 0" class="text-center text-gray-500 py-12">
                Belum ada proyek portfolio yang dipublikasikan.
            </div>

            <div v-else class="divide-y divide-gray-200">
                <div
                    v-for="(project, index) in projects"
                    :key="project.id"
                    class="group py-14 md:py-20 first:pt-0 grid grid-cols-1 md:grid-cols-2 gap-10 md:gap-16 items-center"
                >
                    <div v-scroll-reveal="{ direction: 'left' }" :class="index % 2 === 1 ? 'md:order-2' : 'md:order-1'">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="text-xs font-semibold text-gray-500 tabular-nums">
                                {{ String(index + 1).padStart(2, "0") }} / {{ String(projects.length).padStart(2, "0") }}
                            </span>
                            <span
                                v-if="project.project_type === 'personal'"
                                class="inline-block bg-gray-100 text-gray-700 text-xs font-medium px-3 py-1 rounded-full"
                            >
                                Personal Project
                            </span>
                        </div>
                        <Link :href="'/portfolio/' + project.slug" class="block">
                            <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-gray-900 mb-4 transition-colors group-hover:text-brand-600">
                                {{ project.name }}
                            </h2>
                        </Link>
                        <p class="text-gray-500 leading-relaxed mb-6 max-w-md">
                            {{ project.short_description }}
                        </p>
                            <a
                            v-if="project.live_url"
                            :href="project.live_url"
                            target="_blank"
                            rel="noopener"
                            class="text-gray-500 hover:text-brand-600 text-sm"
                        >
                            {{ domainLabel(project.live_url) }}
                        </a>
                        <Link
                            v-else
                            :href="'/portfolio/' + project.slug"
                            class="text-brand-600 hover:underline text-sm font-medium"
                        >
                            Lihat detail proyek
                        </Link>
                    </div>

                    <Link
                        v-scroll-reveal="{ direction: 'right' }"
                        :href="'/portfolio/' + project.slug"
                        class="block"
                        :class="index % 2 === 1 ? 'md:order-1' : 'md:order-2'"
                    >
                        <div class="rounded-lg overflow-hidden shadow-soft aspect-[4/3]">
                            <img
                                v-if="project.thumbnail"
                                :src="project.thumbnail"
                                :alt="project.name"
                                width="800"
                                height="600"
                                loading="lazy"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                            <div v-else class="w-full h-full bg-gray-100"></div>
                        </div>
                    </Link>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>