<script setup>
/**
 * Section heading for the dark "space" homepage: HUD-style mono index
 * label ("[ 03 ] — LAYANAN") over a hairline rule, then an oversized
 * display-face title. Left-aligned by default (the reference look);
 * `center` for sections that read better symmetric.
 */
defineProps({
    index: { type: [Number, String], default: null },
    label: { type: String, default: "" },
    title: { type: String, default: "" },
    center: { type: Boolean, default: false },
});
</script>

<template>
    <div v-scroll-reveal class="mb-12 md:mb-16" :class="center ? 'text-center max-w-3xl mx-auto' : ''">
        <div
            class="flex items-center gap-4 font-mono text-[10px] md:text-[11px] uppercase tracking-[0.25em] text-white/45 mb-6"
            :class="center ? 'justify-center' : ''"
        >
            <span v-if="index !== null" class="text-brand-400">[ {{ String(index).padStart(2, "0") }} ]</span>
            <span>{{ label }}</span>
            <span v-if="!center" class="h-px flex-1 bg-gradient-to-r from-white/20 to-transparent"></span>
        </div>
        <h2 class="font-display font-semibold text-3xl sm:text-4xl md:text-5xl lg:text-6xl leading-[1.02] tracking-tight text-white [text-wrap:balance]">
            {{ title }}
        </h2>
        <slot />
    </div>
</template>
