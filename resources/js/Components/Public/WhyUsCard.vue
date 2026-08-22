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
});
</script>

<template>
    <div class="why-us-tile relative text-center sm:text-left">
        <span class="block text-5xl md:text-6xl font-extrabold text-brand-200 leading-none mb-5 tabular-nums" aria-hidden="true">
            {{ String(index + 1).padStart(2, "0") }}
        </span>

        <div class="flex items-center justify-center sm:justify-start gap-2.5 mb-2.5">
            <LucideIcon :name="icon" class="w-5 h-5 text-brand-600 shrink-0" />
            <h3 class="font-bold text-lg text-gray-900 tracking-tight">{{ title }}</h3>
        </div>

        <p class="text-base text-gray-600 leading-relaxed max-w-xs mx-auto sm:mx-0">{{ description }}</p>
    </div>
</template>
