<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import SpacePageHeader from "@/Components/Public/SpacePageHeader.vue";
import SpaceSectionHeading from "@/Components/Public/SpaceSectionHeading.vue";
import CircularLogoLoop from "@/Components/CircularLogoLoop.vue";
import LucideIcon from "@/Components/Public/LucideIcon.vue";
import ContactCtaForm from "@/Components/ContactCtaForm.vue";
import { usePage } from "@inertiajs/vue3";
import { computed, onBeforeUnmount, onMounted, ref } from "vue";
import { prefersReducedMotion } from "@/composables/scrollRevealCore";

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

// Star-chart stats: each figure gets its own size and vertical drift.
const statSize = ["text-6xl md:text-8xl", "text-4xl md:text-5xl", "text-5xl md:text-7xl", "text-4xl md:text-6xl"];
const statLayout = ["md:-translate-y-10", "md:translate-y-12", "md:-translate-y-4", "md:translate-y-16"];

// Values console: auto-advances every VALUE_DWELL ms (the progress bar under
// the active item runs for the same duration), pauses while hovered, and
// jumps immediately on click/focus.
const VALUE_DWELL = 4500;
const activeValue = ref(0);
const valuesPaused = ref(false);
const cycleKey = ref(0);
let valueTimer = null;

function selectValue(index) {
    activeValue.value = index;
    cycleKey.value++;
}

onMounted(() => {
    if (!props.about?.values?.length || prefersReducedMotion()) return;
    valueTimer = setInterval(() => {
        if (valuesPaused.value) return;
        selectValue((activeValue.value + 1) % props.about.values.length);
    }, VALUE_DWELL);
});
onBeforeUnmount(() => clearInterval(valueTimer));
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

            <!-- Stats as a star chart: numbers at different sizes and heights,
                 strung on one dashed orbit line, instead of four equal cells. -->
            <div v-if="stats?.length" class="relative mt-20 md:mt-28">
                <svg class="hidden md:block absolute inset-x-0 top-1/2 -translate-y-1/2 w-full h-40 pointer-events-none" viewBox="0 0 1000 160" preserveAspectRatio="none" aria-hidden="true">
                    <path d="M0 120 C 200 20, 380 150, 560 70 S 860 10, 1000 90" fill="none" stroke="rgb(var(--ink) / 0.15)" stroke-width="1" stroke-dasharray="4 6" vector-effect="non-scaling-stroke" />
                </svg>
                <dl class="relative grid grid-cols-2 md:flex md:items-center md:justify-between gap-y-10 gap-x-6">
                    <!-- drift (translate) on the outer box, reveal on the inner one:
                         the reveal directive writes its own inline transform. -->
                    <div
                        v-for="(stat, index) in stats"
                        :key="stat.label"
                        class="relative"
                        :class="statLayout[index % statLayout.length]"
                    >
                      <div v-scroll-reveal="{ delay: index * 100 }">
                        <span class="absolute -left-3 top-1 w-1.5 h-1.5 rounded-full bg-brand-400 shadow-[0_0_12px_rgb(var(--lime-400))]" aria-hidden="true"></span>
                        <dt class="font-mono text-[10px] uppercase tracking-[0.2em] text-white/45 mb-2">/{{ pad(index + 1) }} &mdash; {{ stat.label }}</dt>
                        <dd class="font-display font-semibold text-brand-300 tabular-nums tracking-tight leading-none" :class="statSize[index % statSize.length]">{{ stat.value }}</dd>
                      </div>
                    </div>
                </dl>
            </div>
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
                            class="block font-display font-extrabold uppercase leading-none tracking-tight text-transparent [-webkit-text-stroke:1px_rgb(var(--ink)/0.25)] text-[clamp(3.5rem,9vw,8rem)] mb-8"
                            aria-hidden="true"
                        >Misi</span>
                        <h2 class="font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400 mb-4">Misi kami</h2>
                        <p class="text-white/75 leading-relaxed text-lg md:text-xl">{{ about.mission }}</p>
                    </div>
                    <div v-if="about.vision" v-scroll-reveal="{ direction: 'right', delay: 100 }" class="relative bg-space-deep p-8 md:p-12 overflow-hidden">
                        <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
                        <span
                            class="relative block font-display font-extrabold uppercase leading-none tracking-tight text-transparent [-webkit-text-stroke:1px_rgb(var(--lime-400)/0.55)] text-[clamp(3.5rem,9vw,8rem)] mb-8"
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
            <!--
                Values as a "core reactor" console instead of 4 equal cards:
                a command list on the left (auto-cycling, with a progress
                bar showing the dwell time), and a big HUD display on the
                right with the active value's icon orbiting in rings.
            -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center">
                <div class="lg:col-span-5" @mouseenter="valuesPaused = true" @mouseleave="valuesPaused = false">
                    <SpaceSectionHeading :index="sectionNumbers.values" label="Nilai" title="Nilai-Nilai Kami" />
                    <ul class="space-y-1" role="tablist" aria-label="Nilai-nilai kami">
                        <li v-for="(value, index) in about.values" :key="value.title" :class="index % 2 === 1 ? 'lg:ml-8' : ''">
                            <button
                                type="button"
                                role="tab"
                                :aria-selected="activeValue === index"
                                :aria-controls="'value-panel'"
                                class="group relative w-full text-left flex items-baseline gap-4 py-4 px-1 focus-visible:outline-none"
                                @click="selectValue(index)"
                                @focus="selectValue(index)"
                            >
                                <span class="font-mono text-[10px] tracking-[0.2em] transition-colors" :class="activeValue === index ? 'text-brand-400' : 'text-white/30'">
                                    &gt; {{ pad(index + 1) }}
                                </span>
                                <span
                                    class="font-display font-semibold uppercase tracking-tight transition-all duration-300 text-2xl md:text-3xl"
                                    :class="activeValue === index ? 'text-white translate-x-1' : 'text-white/25 group-hover:text-white/60'"
                                >{{ value.title }}</span>
                                <span class="absolute left-0 right-0 bottom-0 h-px bg-white/10 overflow-hidden" aria-hidden="true">
                                    <span
                                        v-if="activeValue === index"
                                        :key="cycleKey"
                                        class="value-progress block h-full bg-brand-400"
                                        :class="{ 'is-paused': valuesPaused }"
                                    ></span>
                                </span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div id="value-panel" role="tabpanel" class="lg:col-span-7 relative">
                    <div class="hud-frame relative p-2">
                        <div class="relative overflow-hidden rounded-xl bg-space-deep min-h-[420px] md:min-h-[480px] p-8 md:p-12 flex flex-col justify-end [container-type:inline-size]">
                            <!-- rings + orbiting icon -->
                            <div class="absolute -right-24 -top-24 w-[420px] h-[420px] pointer-events-none" aria-hidden="true">
                                <span v-for="n in 4" :key="n" class="absolute rounded-full border" :class="n % 2 ? 'border-white/10 border-dashed' : 'border-brand-400/20'" :style="{ inset: n * 11 + '%' }"></span>
                                <span class="value-orbit absolute inset-[11%]">
                                    <span class="absolute left-1/2 -top-6 -translate-x-1/2 w-12 h-12 rounded-full bg-brand-400 text-space-void flex items-center justify-center shadow-[0_0_40px_-4px_rgb(var(--lime-400)/0.8)]">
                                        <LucideIcon :name="valueIcon(about.values[activeValue])" class="w-5 h-5" />
                                    </span>
                                </span>
                            </div>
                            <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
                            <span class="absolute top-6 left-8 font-mono text-[10px] uppercase tracking-[0.25em] text-white/35">Core value &middot; {{ pad(activeValue + 1) }} / {{ pad(about.values.length) }}</span>

                            <Transition
                                mode="out-in"
                                enter-active-class="transition-all duration-500 ease-out"
                                enter-from-class="opacity-0 translate-y-4 blur-sm"
                                enter-to-class="opacity-100 translate-y-0 blur-0"
                                leave-active-class="transition-all duration-200 ease-in"
                                leave-from-class="opacity-100"
                                leave-to-class="opacity-0 -translate-y-2"
                            >
                                <div :key="activeValue" class="relative">
                                    <p
                                        class="font-display font-extrabold uppercase leading-[0.9] tracking-tight text-transparent [-webkit-text-stroke:1px_rgb(var(--lime-400)/0.6)] text-[min(8cqw,5.5rem)] mb-6 whitespace-nowrap"
                                        aria-hidden="true"
                                    >{{ about.values[activeValue].title }}</p>
                                    <h3 class="sr-only">{{ about.values[activeValue].title }}</h3>
                                    <p class="text-lg md:text-xl text-white/75 leading-relaxed max-w-lg">{{ about.values[activeValue].description }}</p>
                                </div>
                            </Transition>
                        </div>
                    </div>
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
    -webkit-text-stroke: 1px rgb(var(--ink) / 0.2);
}
.ticker-sep {
    color: rgb(var(--lime-400));
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

/* Values console: dwell-time progress bar + slowly orbiting icon. */
.value-progress {
    transform-origin: left;
    animation: value-progress 4500ms linear forwards;
}
.value-progress.is-paused {
    animation-play-state: paused;
}
@keyframes value-progress {
    from { transform: scaleX(0); }
    to { transform: scaleX(1); }
}
.value-orbit {
    animation: value-orbit 18s linear infinite;
}
@keyframes value-orbit {
    to { transform: rotate(360deg); }
}
@media (prefers-reduced-motion: reduce) {
    .value-progress,
    .value-orbit {
        animation: none;
    }
}

/* Tech logos are often dark marks: sit each one on a light chip so it stays legible on the void. */
.tech-loop :deep(.logo) {
    background: rgb(var(--ink) / 0.92);
    border-radius: 9999px;
    padding: 18%;
    box-shadow: 0 0 0 1px rgb(var(--ink) / 0.1), 0 0 30px -10px rgb(var(--lime-400) / 0.5);
}
</style>
