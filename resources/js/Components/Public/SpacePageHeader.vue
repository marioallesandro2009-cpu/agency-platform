<script setup>
/**
 * Page header for inner pages on the dark "space" theme (Inertia twin of
 * resources/views/components/public/space-page-header.blade.php): HUD mono
 * label, oversized display title, slow CSS orbit rings on the right. Leaves
 * room at the top for PublicLayout's fixed transparent space header.
 */
import Breadcrumbs from "./Breadcrumbs.vue";

defineProps({
    eyebrow: { type: String, default: "" },
    code: { type: String, default: "00" },
    title: { type: String, required: true },
    subtitle: { type: String, default: "" },
    breadcrumbItems: { type: Array, default: null },
});
</script>

<template>
    <section class="relative overflow-hidden pt-36 pb-16 md:pt-44 md:pb-24 border-b border-white/10">
        <!-- Art composition (no grid): giant cropped outline word, a diagonal
             light-trail hairline, off-canvas orbit arcs and a star-chart label. -->
        <span class="space-page-header__word" aria-hidden="true">{{ eyebrow }}</span>
        <span class="space-page-header__trail" aria-hidden="true"></span>
        <span class="hidden md:block absolute right-[6%] top-[22%] font-mono text-[9px] uppercase tracking-[0.3em] text-white/30 pointer-events-none" aria-hidden="true">RA 14h 29m &middot; DEC &minus;62&deg;</span>
        <div class="space-page-header__orbits pointer-events-none" aria-hidden="true">
            <span v-for="n in 5" :key="n" class="orbit" :style="{ '--i': n }"></span>
        </div>
        <div
            class="absolute inset-0 pointer-events-none"
            aria-hidden="true"
            style="background: radial-gradient(circle at 85% 50%, rgb(var(--lime-400) / 0.1), transparent 45%)"
        ></div>

        <div class="relative max-w-[1440px] mx-auto px-4 md:px-8">
            <Breadcrumbs v-if="breadcrumbItems?.length" variant="space" :items="breadcrumbItems" />
            <p v-scroll-reveal class="flex items-center gap-3 font-mono text-[10px] md:text-[11px] uppercase tracking-[0.25em] text-white/55 mb-6">
                <span class="text-brand-400">[ {{ code }} ]</span>
                <span class="h-px w-10 bg-white/25"></span>
                {{ eyebrow }}
            </p>
            <h1
                v-scroll-reveal="{ delay: 80 }"
                class="font-display font-semibold text-[2rem] sm:text-5xl lg:text-6xl xl:text-7xl leading-[1.02] tracking-tight text-white max-w-5xl mb-6 [text-wrap:balance]"
            >
                {{ title }}
            </h1>
            <p v-if="subtitle" v-scroll-reveal="{ delay: 160 }" class="text-base md:text-lg text-white/60 leading-relaxed max-w-2xl">
                {{ subtitle }}
            </p>
            <div v-if="$slots.default" v-scroll-reveal="{ delay: 240 }" class="mt-8">
                <slot />
            </div>
        </div>
    </section>
</template>
