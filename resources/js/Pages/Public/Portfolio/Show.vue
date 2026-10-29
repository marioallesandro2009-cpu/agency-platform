<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import SpacePageHeader from "@/Components/Public/SpacePageHeader.vue";
import SpaceSectionHeading from "@/Components/Public/SpaceSectionHeading.vue";
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

const pad = (n) => String(n).padStart(2, "0");

// "[ 0X ]" numbers for whichever optional case-study blocks this project has.
const blockNumbers = computed(() => {
    const blocks = {
        about: !!props.project.description,
        challenge: !!props.project.challenge,
        features: !!props.project.features?.length,
        tech: !!props.project.technologies?.length,
    };
    let n = 0;
    return Object.fromEntries(Object.entries(blocks).map(([key, on]) => [key, on ? ++n : null]));
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
        :title="project.seo_title || project.name"
        :description="project.seo_description || project.short_description"
        :image="project.thumbnail"
    />
    <PublicLayout theme="space">
        <SpacePageHeader
            :eyebrow="project.category || 'Portfolio'"
            code="WRK"
            :title="project.name"
            :subtitle="project.short_description"
            :breadcrumb-items="breadcrumbItems"
        >
            <div class="flex flex-wrap items-center gap-3">
                <span
                    v-if="project.project_type === 'personal'"
                    class="font-mono text-[10px] uppercase tracking-[0.18em] text-white/70 border border-white/15 px-2.5 py-1 rounded-full"
                >
                    Contoh Kapabilitas
                </span>
                <span v-if="project.client_label" class="font-mono text-[10px] uppercase tracking-[0.18em] text-white/45">{{ project.client_label }}</span>
            </div>
        </SpacePageHeader>

        <div class="max-w-[1440px] mx-auto px-4 md:px-8 pt-12 md:pt-16">
            <div v-scroll-reveal="{ delay: 100 }" class="hud-frame relative p-2">
                <div class="relative h-64 md:h-[32rem] rounded-lg overflow-hidden bg-space-panel">
                    <img
                        v-if="project.cover_image || project.thumbnail"
                        :src="project.cover_image || project.thumbnail"
                        :alt="project.name"
                        width="1400"
                        height="512"
                        class="w-full h-full object-cover"
                    />
                    <div v-else class="w-full h-full bg-grid-subtle" aria-hidden="true"></div>
                </div>
            </div>
        </div>

        <!-- Spec strip -->
        <div class="max-w-[1440px] mx-auto px-4 md:px-8 mt-6">
            <dl class="grid grid-cols-2 md:grid-cols-4 gap-px rounded-2xl overflow-hidden border border-white/10 bg-white/10 font-mono text-[10px] uppercase tracking-[0.2em]">
                <div class="bg-space-void px-5 py-4">
                    <dt class="text-white/35 mb-1.5">Kategori</dt>
                    <dd class="text-white/85">{{ project.category || "—" }}</dd>
                </div>
                <div class="bg-space-void px-5 py-4">
                    <dt class="text-white/35 mb-1.5">Tipe</dt>
                    <dd class="text-white/85">{{ project.project_type === "personal" ? "Konsep" : "Klien" }}</dd>
                </div>
                <div class="bg-space-void px-5 py-4">
                    <dt class="text-white/35 mb-1.5">Stack</dt>
                    <dd class="text-white/85 truncate">{{ project.technologies?.length ? project.technologies.slice(0, 2).join(" / ") : "—" }}</dd>
                </div>
                <div class="bg-space-void px-5 py-4">
                    <dt class="text-white/35 mb-1.5">Live</dt>
                    <dd>
                        <a v-if="project.live_url" :href="project.live_url" target="_blank" rel="noopener" class="text-brand-400 hover:text-brand-300 truncate block">
                            {{ domainLabel(project.live_url) }} &nearr;
                        </a>
                        <span v-else class="text-white/85">—</span>
                    </dd>
                </div>
            </dl>
        </div>

        <section class="max-w-[1440px] mx-auto px-4 md:px-8 py-20 md:py-28 space-y-20 md:space-y-24">
            <div v-if="blockNumbers.about" class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-12">
                <p class="lg:col-span-4 font-mono text-[10px] md:text-[11px] uppercase tracking-[0.25em] text-white/45">
                    <span class="text-brand-400">[ {{ pad(blockNumbers.about) }} ]</span> Tentang proyek
                </p>
                <p v-scroll-reveal class="lg:col-span-8 font-display text-xl md:text-3xl leading-snug text-white/85 tracking-tight [text-wrap:pretty]">
                    {{ project.description }}
                </p>
            </div>

            <div v-if="blockNumbers.challenge" class="grid grid-cols-1 md:grid-cols-2 gap-px rounded-2xl overflow-hidden border border-white/10 bg-white/10">
                <div v-scroll-reveal class="bg-space-void p-7 md:p-10">
                    <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-white/45 mb-6">
                        <span class="text-brand-400">[ {{ pad(blockNumbers.challenge) }} ]</span> Tantangan
                    </p>
                    <p class="text-white/70 leading-relaxed">{{ project.challenge }}</p>
                </div>
                <div v-if="project.solution" v-scroll-reveal="{ delay: 80 }" class="relative bg-space-deep p-7 md:p-10">
                    <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
                    <p class="relative font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400 mb-6">&rarr; Solusi</p>
                    <p class="relative text-white/85 leading-relaxed">{{ project.solution }}</p>
                </div>
            </div>

            <div v-if="blockNumbers.features">
                <SpaceSectionHeading :index="blockNumbers.features" label="Fitur" title="Yang Kami Bangun" />
                <ul class="grid grid-cols-1 md:grid-cols-2 gap-px rounded-2xl overflow-hidden border border-white/10 bg-white/10">
                    <li
                        v-for="(feature, i) in project.features"
                        :key="feature"
                        v-scroll-reveal="{ delay: Math.min(i * 50, 250) }"
                        class="bg-space-void p-6 flex gap-5 transition-colors hover:bg-space-deep"
                    >
                        <span class="font-mono text-[10px] tracking-[0.2em] text-brand-400 pt-1 shrink-0">/{{ pad(i + 1) }}</span>
                        <span class="text-white/80">{{ feature }}</span>
                    </li>
                </ul>
            </div>

            <div v-if="blockNumbers.tech">
                <SpaceSectionHeading :index="blockNumbers.tech" label="Teknologi" title="Stack yang Dipakai" />
                <div class="flex flex-wrap gap-2.5">
                    <span
                        v-for="tech in project.technologies"
                        :key="tech"
                        class="font-mono text-[11px] uppercase tracking-[0.15em] text-white/80 border border-white/15 bg-white/[0.03] px-4 py-2 rounded-full transition-colors hover:border-brand-400 hover:text-brand-300"
                    >
                        {{ tech }}
                    </span>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-3xl border border-brand-400/30 bg-space-deep px-6 py-12 md:px-14 md:py-16 flex flex-col md:flex-row md:items-center md:justify-between gap-8">
                <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
                <div class="relative">
                    <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400 mb-4">[ &rarr; ] Langkah berikutnya</p>
                    <h2 class="font-display font-semibold text-2xl md:text-4xl text-white tracking-tight mb-2 [text-wrap:balance]">Tertarik dengan hasil serupa?</h2>
                    <p class="text-white/55">Konsultasikan kebutuhan website Anda secara gratis.</p>
                </div>
                <div class="relative flex flex-wrap gap-3 shrink-0">
                    <Link href="/konsultasi" class="btn-space btn-space--lg btn-space--pill">
                        Konsultasi Gratis <span class="btn-space__arrow" aria-hidden="true">&rarr;</span>
                    </Link>
                    <a v-if="project.live_url" :href="project.live_url" target="_blank" rel="noopener" class="btn-space btn-space--lg btn-space--glass">
                        Kunjungi situs <span class="btn-space__arrow" aria-hidden="true">&nearr;</span>
                    </a>
                </div>
            </div>
        </section>

        <section v-if="related.length" class="max-w-[1440px] mx-auto px-4 md:px-8 pb-24 md:pb-32">
            <SpaceSectionHeading label="Lainnya" title="Proyek Terkait" />
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <Link
                    v-for="(item, index) in related"
                    :key="item.id"
                    :href="'/portfolio/' + item.slug"
                    v-scroll-reveal="{ delay: index * 80 }"
                    class="group block"
                >
                    <div class="aspect-[4/3] rounded-xl overflow-hidden bg-space-panel ring-1 ring-white/10 mb-5 transition-shadow group-hover:ring-brand-400/40">
                        <img
                            v-if="item.thumbnail"
                            :src="item.thumbnail"
                            :alt="item.name"
                            width="400"
                            height="300"
                            loading="lazy"
                            class="w-full h-full object-cover opacity-85 transition-all duration-500 group-hover:scale-105 group-hover:opacity-100"
                        />
                    </div>
                    <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-400 mb-2">{{ item.category }}</p>
                    <h3 class="font-display font-semibold text-lg text-white tracking-tight transition-colors group-hover:text-brand-300">{{ item.name }}</h3>
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>
