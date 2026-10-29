<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import SpacePageHeader from "@/Components/Public/SpacePageHeader.vue";
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

const pad = (n) => String(n).padStart(2, "0");
</script>

<template>
    <SeoHead
        title="Portfolio"
        description="Kumpulan proyek website yang sudah kami kerjakan untuk berbagai bisnis dan UMKM."
    />
    <PublicLayout theme="space">
        <SpacePageHeader
            eyebrow="Portfolio"
            code="WRK"
            title="Hasil Karya Kami"
            subtitle="Beberapa proyek konsep yang menunjukkan pendekatan kami dalam membangun website untuk berbagai kebutuhan bisnis."
        >
            <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-white/45">
                <span class="text-brand-400">{{ pad(projects.length) }}</span> proyek terdokumentasi
            </p>
        </SpacePageHeader>

        <section class="max-w-[1440px] mx-auto px-4 md:px-8 py-16 md:py-24">
            <div v-if="projects.length === 0" class="rounded-2xl border border-dashed border-white/15 text-center py-20 px-6">
                <p class="font-mono text-[11px] uppercase tracking-[0.2em] text-white/45">Belum ada proyek portfolio yang dipublikasikan.</p>
            </div>

            <div v-else class="space-y-6 md:space-y-8">
                <article
                    v-for="(project, index) in projects"
                    :key="project.id"
                    class="group relative grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center rounded-2xl border border-white/10 bg-white/[0.02] p-4 md:p-6 transition-colors duration-300 hover:border-brand-400/40"
                >
                    <Link
                        v-scroll-reveal="{ direction: index % 2 === 1 ? 'left' : 'right' }"
                        :href="'/portfolio/' + project.slug"
                        class="lg:col-span-7 block focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-400 rounded-lg"
                        :class="index % 2 === 1 ? 'lg:order-2' : 'lg:order-1'"
                        :aria-label="project.name"
                    >
                        <div class="hud-frame relative p-2">
                            <div class="aspect-[16/10] rounded-lg overflow-hidden bg-space-panel">
                                <img
                                    v-if="project.thumbnail"
                                    :src="project.thumbnail"
                                    :alt="project.name"
                                    width="800"
                                    height="500"
                                    loading="lazy"
                                    class="w-full h-full object-cover opacity-85 transition-all duration-700 group-hover:scale-105 group-hover:opacity-100"
                                />
                                <div
                                    v-else
                                    class="w-full h-full bg-grid-subtle flex items-center justify-center font-mono text-[10px] uppercase tracking-[0.25em] text-white/35"
                                    aria-hidden="true"
                                >
                                    WRK-{{ String(index + 1).padStart(3, "0") }}
                                </div>
                            </div>
                        </div>
                    </Link>

                    <div
                        v-scroll-reveal="{ direction: index % 2 === 1 ? 'right' : 'left', delay: 80 }"
                        class="lg:col-span-5 px-2 md:px-4 pb-4 lg:pb-0"
                        :class="index % 2 === 1 ? 'lg:order-1' : 'lg:order-2'"
                    >
                        <div class="flex flex-wrap items-center gap-3 mb-6">
                            <span class="font-mono text-[10px] uppercase tracking-[0.25em] text-white/45 tabular-nums">
                                <span class="text-brand-400">{{ pad(index + 1) }}</span> / {{ pad(projects.length) }}
                            </span>
                            <span v-if="project.category" class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-400">{{ project.category }}</span>
                            <span
                                v-if="project.project_type === 'personal'"
                                class="font-mono text-[10px] uppercase tracking-[0.18em] text-white/70 border border-white/15 px-2.5 py-1 rounded-full"
                            >
                                Contoh Kapabilitas
                            </span>
                        </div>
                        <Link :href="'/portfolio/' + project.slug" class="block">
                            <h2 class="font-display font-semibold text-3xl md:text-4xl leading-[1.08] tracking-tight text-white mb-4 transition-colors group-hover:text-brand-300 [text-wrap:balance]">
                                {{ project.name }}
                            </h2>
                        </Link>
                        <p class="text-white/55 leading-relaxed mb-8 max-w-md">
                            {{ project.short_description }}
                        </p>
                        <div class="flex flex-wrap items-center gap-3">
                            <Link :href="'/portfolio/' + project.slug" class="btn-space btn-space--glass">
                                Lihat studi kasus <span class="btn-space__arrow" aria-hidden="true">&rarr;</span>
                            </Link>
                            <a
                                v-if="project.live_url"
                                :href="project.live_url"
                                target="_blank"
                                rel="noopener"
                                class="font-mono text-[10px] uppercase tracking-[0.18em] text-white/45 hover:text-brand-300 transition-colors"
                            >
                                {{ domainLabel(project.live_url) }} &nearr;
                            </a>
                        </div>
                    </div>
                </article>
            </div>
        </section>

        <section class="max-w-[1440px] mx-auto px-4 md:px-8 pb-24 md:pb-32">
            <div v-scroll-reveal class="relative overflow-hidden rounded-3xl border border-brand-400/30 bg-space-deep px-6 py-14 md:px-14 md:py-20 text-center">
                <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
                <p class="relative font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400 mb-5">[ &rarr; ] Proyek berikutnya</p>
                <h2 class="relative font-display font-semibold text-3xl md:text-5xl text-white tracking-tight mb-5 [text-wrap:balance]">Proyek Anda Bisa Jadi yang Berikutnya</h2>
                <p class="relative text-white/60 mb-10">Konsultasikan kebutuhan website Anda secara gratis, tanpa komitmen.</p>
                <Link href="/konsultasi" class="relative btn-space btn-space--lg btn-space--pill">
                    Konsultasi Gratis <span class="btn-space__arrow" aria-hidden="true">&rarr;</span>
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>
