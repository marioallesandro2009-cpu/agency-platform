<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import PageHeaderScanner from "@/Components/Public/PageHeaderScanner.vue";
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

// Single subtle background accent for the Misi/Visi hero-typography section
// (see below) — a low-opacity scrolling word strip, not tied to any DB data
// since it's pure brand copy, not a statistic.
const tickerText = "Terpercaya • Cepat • Profesional • ".repeat(6);

// Sensible default icons for the 4 known "Nilai-Nilai" cards, used only
// until an admin picks a real icon via the new picker (content.values.*.icon
// is null for every value that predates this feature, including whatever
// is already saved in production).
const VALUE_ICON_FALLBACK = {
    "Transparansi": "shield-check",
    "Kualitas": "check-circle",
    "Kolaboratif": "users",
    "Tepat Waktu": "clock",
};

function valueIcon(value) {
    return value.icon || VALUE_ICON_FALLBACK[value.title] || "";
}
</script>

<template>
    <SeoHead
        title="Tentang Kami"
        :description="about?.description || 'Kenali lebih dekat tim dan visi misi kami dalam membangun website untuk bisnis Anda.'"
    />
    <PublicLayout>
        <PageHeaderScanner
            :eyebrow="about?.subtitle || 'Siapa Kami'"
            :title="about?.title || 'Tentang Kami'"
        />

        <section v-if="about?.description" class="max-w-7xl mx-auto px-4 py-20 md:py-24">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 md:gap-12 items-start">
                <div v-scroll-reveal="{ direction: 'left' }">
                    <img
                        v-if="mascots.intro_tentang_kami?.image_path"
                        :src="mascots.intro_tentang_kami.image_path"
                        :alt="mascots.intro_tentang_kami.alt_text || ''"
                        width="80"
                        height="80"
                        loading="lazy"
                        class="w-20 h-20 object-contain mb-4"
                    />
                    <p class="text-brand-600 font-semibold text-xs uppercase tracking-widest mb-3">Profil Kami</p>
                    <p class="text-left text-gray-600 leading-relaxed text-lg">
                        {{ about.description }}
                    </p>
                </div>
                <div v-scroll-reveal="{ direction: 'right', delay: 100 }" class="aspect-[4/3] rounded-lg overflow-hidden bg-gray-100">
                    <img
                        :src="about.image || mascots.hero_tentang_kami?.image_path || 'https://placehold.co/600x450'"
                        :alt="about.image || mascots.hero_tentang_kami?.image_path ? (about.title || 'Tentang Kami') : ''"
                        :aria-hidden="!about.image && !mascots.hero_tentang_kami?.image_path"
                        width="600"
                        height="450"
                        loading="lazy"
                        class="w-full h-full object-cover"
                    />
                </div>
            </div>

            <div v-if="stats?.length" class="grid grid-cols-2 md:grid-cols-4 divide-x divide-gray-200 border-y border-gray-200 mt-16 md:mt-20 py-10">
                <div v-for="(stat, index) in stats" :key="stat.label" v-scroll-reveal="{ delay: index * 80 }" class="text-center px-4">
                    <p class="text-4xl md:text-5xl font-extrabold text-brand-600 tabular-nums">{{ stat.value }}</p>
                    <p class="text-sm text-gray-500 mt-2">{{ stat.label }}</p>
                </div>
            </div>
        </section>

        <!-- Misi & Visi: big-typography hero treatment — no icons/cards,
             text itself is the visual element. Misi sits top-left, Visi
             bottom-right, separated by a single green marquee divider line
             (the one background accent) instead of icons/cards.
             The admin-editable mascot slot (mascots.misi_visi_tentang_kami)
             is intentionally not rendered here since an overlaid
             illustration would compete with the type; the upload slot in
             /admin/maskot is untouched in case a future design wants it. -->
        <section v-if="about?.mission || about?.vision" class="bg-white">
            <div v-if="about.mission" v-scroll-reveal="{ direction: 'left' }" class="max-w-5xl mx-auto py-20 md:py-28 px-4">
                <p class="text-brand-600 font-semibold text-xs md:text-sm uppercase tracking-[0.3em] mb-4">Misi Kami</p>
                <h2 class="font-extrabold text-gray-900 leading-[1.05] mb-6 text-[clamp(2.5rem,7vw,5.5rem)]">
                    <span class="border-b-[6px] md:border-b-8 border-brand-500">Misi</span>
                </h2>
                <p class="text-gray-600 leading-relaxed max-w-2xl text-[clamp(1.125rem,2vw,1.5rem)]">{{ about.mission }}</p>
            </div>

            <!-- Single subtle accent: a bold green scrolling word strip
                 dividing the two blocks, standing in for icons/cards. -->
            <div class="ticker-row" aria-hidden="true">
                <div class="ticker-track flex whitespace-nowrap">
                    <span class="ticker-item">{{ tickerText }}</span>
                    <span class="ticker-item">{{ tickerText }}</span>
                </div>
            </div>

            <div v-if="about.vision" v-scroll-reveal="{ direction: 'right', delay: 100 }" class="max-w-5xl mx-auto py-20 md:py-28 px-4 text-right">
                <p class="text-brand-600 font-semibold text-xs md:text-sm uppercase tracking-[0.3em] mb-4">Visi Kami</p>
                <h2 class="font-extrabold text-gray-900 leading-[1.05] mb-6 text-[clamp(2.5rem,7vw,5.5rem)]">
                    <span class="border-b-[6px] md:border-b-8 border-brand-500">Visi</span>
                </h2>
                <p class="text-gray-600 leading-relaxed max-w-2xl ml-auto text-[clamp(1.125rem,2vw,1.5rem)]">{{ about.vision }}</p>
            </div>
        </section>

        <section v-if="about?.values?.length" class="max-w-7xl mx-auto px-4 py-20 md:py-24">
            <h2 v-scroll-reveal class="text-3xl font-bold tracking-tight text-gray-900 text-center mb-14">Nilai-Nilai Kami</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div v-for="(value, index) in about.values" :key="value.title" v-scroll-reveal="{ delay: index * 80 }">
                    <WhyUsCard :title="value.title" :description="value.description" :icon="valueIcon(value)" :index="index" />
                </div>
            </div>
        </section>

        <section v-if="techLogos.length" class="py-16 md:py-20 border-t border-gray-100">
            <div v-scroll-reveal class="max-w-4xl mx-auto px-4 text-center">
                <h2 class="text-2xl font-bold text-gray-900 mb-10">Teknologi yang Kami Gunakan</h2>
                <CircularLogoLoop :logos="techLogos" :size="88" aria-label="Teknologi yang kami gunakan" />
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
    padding: 1rem 0;
    -webkit-mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);
    mask-image: linear-gradient(to right, transparent, black 8%, black 92%, transparent);
}

.ticker-track {
    width: max-content;
    animation: ticker-scroll 28s linear infinite;
}

.ticker-item {
    display: inline-block;
    padding-right: 2rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.02em;
    color: #4d7c0f;
    font-size: clamp(1rem, 2.6vw, 1.75rem);
}

@keyframes ticker-scroll {
    from {
        transform: translateX(0);
    }
    to {
        transform: translateX(-50%);
    }
}

@media (prefers-reduced-motion: reduce) {
    .ticker-track {
        animation: none;
    }
}
</style>