<script setup>
/**
 * "Why Us" tile: large pale numeral + icon + title + description, no card
 * frame/border/shadow. Deliberately distinct from ServiceCard's divider-list
 * treatment so the two sections don't read as the same component reskinned.
 */
import LucideIcon from "./LucideIcon.vue";

defineProps({
    title: { type: String, required: true },
    description: { type: String, default: "" },
    icon: { type: String, default: "sparkles" },
    index: { type: Number, default: 0 },
    // Dark "space" homepage variant: glass tile, outlined numeral.
    dark: { type: Boolean, default: false },
});
</script>

<template>
    <div
        v-if="dark"
        class="group relative h-full rounded-2xl border border-white/10 bg-white/[0.03] p-6 md:p-7 transition-all duration-300 hover:-translate-y-1 hover:border-brand-400/40 hover:bg-white/[0.05]"
    >
        <div class="flex items-center justify-between mb-10">
            <div class="w-11 h-11 rounded-full border border-white/15 flex items-center justify-center text-brand-400 transition-colors group-hover:bg-brand-400 group-hover:text-space-void">
                <LucideIcon :name="icon" class="w-5 h-5" />
            </div>
            <span class="font-mono text-[10px] tracking-[0.2em] text-white/35" aria-hidden="true">/{{ String(index + 1).padStart(2, "0") }}</span>
        </div>
        <h3 class="font-display font-semibold text-lg text-white tracking-tight mb-3">{{ title }}</h3>
        <p class="text-sm text-white/55 leading-relaxed">{{ description }}</p>
    </div>
    <div v-else class="why-us-tile relative pl-5 sm:pl-6 border-l-2 border-brand-100">
        <span class="block text-5xl md:text-6xl font-extrabold text-brand-200 leading-none mb-5 tabular-nums" aria-hidden="true">
            {{ String(index + 1).padStart(2, "0") }}
        </span>

        <div class="flex items-center gap-2.5 mb-2.5">
            <LucideIcon :name="icon" class="w-5 h-5 text-brand-600 shrink-0" />
            <h3 class="font-bold text-lg text-gray-900 tracking-tight">{{ title }}</h3>
        </div>

        <p class="text-base text-gray-600 leading-relaxed max-w-xs">{{ description }}</p>
    </div>
</template>
