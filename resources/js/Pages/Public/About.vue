<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import SpacePageHeader from "@/Components/Public/SpacePageHeader.vue";
import SpaceSectionHeading from "@/Components/Public/SpaceSectionHeading.vue";
import CircularLogoLoop from "@/Components/CircularLogoLoop.vue";
import WhyUsCard from "@/Components/Public/WhyUsCard.vue";
import ContactCtaForm from "@/Components/ContactCtaForm.vue";
import { usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    about: Object,
    techStacks: Array,
    stats: Array,
});

const techLogos = computed(() =>
    (props.techStacks || []).map((item) => ({
        src: item.icon_url,
        alt: item.name,
        title: item.name,
        href: item.link_url || undefined,
    }))
);

const mascots = usePage().props.mascots || {};

// Brand-copy word strip between Misi and Visi (not DB data).
const tickerWords = ["Terpercaya", "Cepat", "Profesional", "Transparan", "Kolaboratif"];

// Sensible default icons for the 4 known "Nilai-Nilai" cards, used only
// until an admin picks a real icon via the picker (content.values.*.icon is
// null for every value that predates that feature). Deliberately no
// check-mark style icons here.
const VALUE_ICON_FALLBACK = {
    "Transparansi": "compass",
    "Kualitas": "star",
    "Kolaboratif": "users",
    "Tepat Waktu": "clock",
};

// Check-mark icons an admin may already have saved are swapped out on the
// public page too (the redesign avoids check marks entirely).
const CHECK_ICONS = ["check-circle", "shield-check"];

function valueIcon(value) {
    const icon = CHECK_ICONS.includes(value.icon) ? null : value.icon;
    return icon || VALUE_ICON_FALLBACK[value.title] || "sparkles";
}

const heroImage = computed(() => props.about?.image || mascots.hero_tentang_kami?.image_path || null);

// "[ 0X ]" numbers for whichever optional sections this page has.
const sectionNumbers = computed(() => {
    const blocks = {
        profile: !!props.about?.description,
        mission: !!(props.about?.mission || props.about?.vision),
        values: !!props.about?.values?.length,
        tech: techLogos.value.length > 0,
    };
    let n = 0;
    return Object.fromEntries(Object.entries(blocks).map(([key, on]) => [key, on ? ++n : null]));
});
const pad = (n) => String(n).padStart(2, "0");
</script>

<template>
    <SeoHead
        title="Tentang Kami"
        :description="about?.description || 'Kenali lebih dekat tim dan visi misi kami dalam membangun website untuk bisnis Anda.'"
    />
    <PublicLayout theme="space">
        <SpacePageHeader
            :eyebrow="about?.subtitle || 'Siapa Kami'"
            code="ABT"
            :title="about?.title || 'Tentang Kami'"
        />

        <!-- Profile + stats -->
        <section v-if="about?.description" class="max-w-[1440px] mx-auto px-4 md:px-8 py-20 md:py-28">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16 items-center">
                <div v-scroll-reveal="{ direction: 'left' }" class="lg:col-span-6 order-2 lg:order-1">
                    <img
                        v-if="mascots.intro_tentang_kami?.image_path"
                        :src="mascots.intro_tentang_kami.image_path"
                        :alt="mascots.intro_tentang_kami.alt_text || ''"
                        width="80"
                        height="80"
                        loading="lazy"
                        class="w-20 h-20 object-contain mb-6"
                    />
                    <p class="flex items-center gap-4 font-mono text-[10px] md:text-[11px] uppercase tracking-[0.25em] text-white/45 mb-6">
                        <span class="text-brand-400">[ {{ pad(sectionNumbers.profile) }} ]</span>
                        Profil kami
                        <span class="h-px flex-1 bg-gradient-to-r from-white/20 to-transparent"></span>
                    </p>
                    <p class="text-lg md:text-xl leading-relaxed text-white/75 [text-wrap:pretty] first-line:text-white">
                        {{ about.description }}
                    </p>
                </div>
                <div v-scroll-reveal="{ direction: 'right', delay: 100 }" class="lg:col-span-6 order-1 lg:order-2">
                    <div class="hud-frame relative p-2">
                        <div class="aspect-[4/3] rounded-lg overflow-hidden bg-space-panel">
                            <img
                                v-if="heroImage"
                                :src="heroImage"
                                :alt="about.title || 'Tentang Kami'"
                                width="700"
                                height="525"
                                loading="lazy"
                                class="w-full h-full object-cover"
                            />
                            <div
                                v-else
                                class="w-full h-full bg-grid-subtle flex items-center justify-center font-mono text-[10px] uppercase tracking-[0.25em] text-white/35"
                                aria-hidden="true"
                            >
                                Crew &middot; ExodineWeb
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <dl v-if="stats?.length" class="grid grid-cols-2 md:grid-cols-4 gap-px rounded-2xl overflow-hidden border border-white/10 bg-white/10 mt-16 md:mt-24">
                <div v-for="(stat, index) in stats" :key="stat.label" v-scroll-reveal="{ delay: index * 80 }" class="bg-space-void px-6 py-8 md:py-10">
                    <dt class="font-mono text-[10px] uppercase tracking-[0.2em] text-white/40 mb-3">/{{ pad(index + 1) }} &mdash; {{ stat.label }}</dt>
                    <dd class="font-display font-semibold text-4xl md:text-6xl text-brand-300 tabular-nums tracking-tight">{{ stat.value }}</dd>
                </div>
            </dl>
        </section>

        <!-- Misi & Visi: the words themselves are the visual, as giant outlined type -->
        <section v-if="about?.mission || about?.vision" class="relative py-20 md:py-28 border-y border-white/10 overflow-hidden">
            <div class="max-w-[1440px] mx-auto px-4 md:px-8">
                <p class="flex items-center gap-4 font-mono text-[10px] md:text-[11px] uppercase tracking-[0.25em] text-white/45 mb-12">
                    <span class="text-brand-400">[ {{ pad(sectionNumbers.mission) }} ]</span>
                    Arah kami
                    <span class="h-px flex-1 bg-gradient-to-r from-white/20 to-transparent"></span>
                </p>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-px rounded-2xl overflow-hidden border border-white/10 bg-white/10">
                    <div v-if="about.mission" v-scroll-reveal="{ direction: 'left' }" class="relative bg-space-void p-8 md:p-12 overflow-hidden">
                        <span
                            class="block font-display font-extrabold uppercase leading-none tracking-tight text-transparent [-webkit-text-stroke:1px_rgba(255,255,255,0.25)] text-[clamp(3.5rem,9vw,8rem)] mb-8"
                            aria-hidden="true"
                        >Misi</span>
                        <h2 class="font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400 mb-4">Misi kami</h2>
                        <p class="text-white/75 leading-relaxed text-lg md:text-xl">{{ about.mission }}</p>
                    </div>
                    <div v-if="about.vision" v-scroll-reveal="{ direction: 'right', delay: 100 }" class="relative bg-space-deep p-8 md:p-12 overflow-hidden">
                        <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
                        <span
                            class="relative block font-display font-extrabold uppercase leading-none tracking-tight text-transparent [-webkit-text-stroke:1px_rgba(163,230,53,0.55)] text-[clamp(3.5rem,9vw,8rem)] mb-8"
                            aria-hidden="true"
                        >Visi</span>
                        <h2 class="relative font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400 mb-4">Visi kami</h2>
                        <p class="relative text-white/85 leading-relaxed text-lg md:text-xl">{{ about.vision }}</p>
                    </div>
                </div>
            </div>

            <div class="ticker-row mt-16 md:mt-20" aria-hidden="true">
                <div class="ticker-track flex whitespace-nowrap">
                    <span v-for="n in 2" :key="n" class="flex items-center">
                        <template v-for="(word, i) in [...tickerWords, ...tickerWords]" :key="n + '-' + i">
                            <span class="ticker-item">{{ word }}</span>
                            <span class="ticker-sep">&#10022;</span>
                        </template>
                    </span>
                </div>
            </div>
        </section>

        <section v-if="about?.values?.length" class="max-w-[1440px] mx-auto px-4 md:px-8 py-20 md:py-28">
            <SpaceSectionHeading :index="sectionNumbers.values" label="Nilai" title="Nilai-Nilai Kami" />
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
                <div v-for="(value, index) in about.values" :key="value.title" v-scroll-reveal="{ delay: index * 80 }">
                    <WhyUsCard :title="value.title" :description="value.description" :icon="valueIcon(value)" :index="index" dark />
                </div>
            </div>
        </section>

        <section v-if="techLogos.length" class="max-w-[1440px] mx-auto px-4 md:px-8 pb-8">
            <div class="rounded-2xl border border-white/10 bg-white/[0.02] px-6 py-12 md:px-12 md:py-14">
                <SpaceSectionHeading :index="sectionNumbers.tech" label="Stack" title="Teknologi yang Kami Gunakan" center />
                <div v-scroll-reveal class="tech-loop max-w-4xl mx-auto">
                    <CircularLogoLoop :logos="techLogos" :size="88" aria-label="Teknologi yang kami gunakan" />
                </div>
            </div>
        </section>

        <ContactCtaForm
            eyebrow="Mulai Sekarang"
            title="Siap Bekerja Sama dengan Kami?"
            description="Ceritakan kebutuhan website Anda, tim kami akan bantu wujudkan dalam waktu singkat."
        />
    </PublicLayout>
</template>

<style scoped>
.ticker-row {
    position: relative;
    overflow: hidden;
    padding: 0.5rem 0;
    -webkit-mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);
    mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);
}
.ticker-track {
    width: max-content;
    animation: ticker-scroll 36s linear infinite;
}
.ticker-item {
    font-family: "Unbounded", "Raleway", sans-serif;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: -0.01em;
    font-size: clamp(1.75rem, 5vw, 4rem);
    color: transparent;
    -webkit-text-stroke: 1px rgba(255, 255, 255, 0.2);
}
.ticker-sep {
    color: #a3e635;
    font-size: clamp(1rem, 2.5vw, 2rem);
    padding: 0 clamp(1rem, 2.5vw, 2rem);
}
@keyframes ticker-scroll {
    to {
        transform: translateX(-50%);
    }
}
@media (prefers-reduced-motion: reduce) {
    .ticker-track {
        animation: none;
    }
}

/* Tech logos are often dark marks: sit each one on a light chip so it stays legible on the void. */
.tech-loop :deep(.logo) {
    background: rgba(255, 255, 255, 0.92);
    border-radius: 9999px;
    padding: 18%;
    box-shadow: 0 0 0 1px rgba(255, 255, 255, 0.1), 0 0 30px -10px rgba(163, 230, 53, 0.5);
}
</style>
