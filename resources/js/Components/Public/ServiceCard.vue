<script setup>
/**
 * Service row: editorial numbered-list treatment (01 — Service Name) instead
 * of a bordered/shadowed card. Divider-led list so the Services section reads
 * differently from WhyUsCard's grid below it — image stays but is de-framed
 * (no card wrapper), features/CTA always visible since there's already an
 * image + "Pelajari lebih lanjut" CTA doing the scannability work.
 */
import { Link } from "@inertiajs/vue3";
import LucideIcon from "./LucideIcon.vue";

defineProps({
    service: { type: Object, required: true },
    index: { type: Number, default: 0 },
});

const PLACEHOLDER = "https://placehold.co/640x400?text=%20";
</script>

<template>
    <div class="group relative py-10 md:py-12 border-t border-gray-200 first:border-t-0">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-10 items-center">
            <div class="lg:col-span-1">
                <span class="text-sm font-semibold text-gray-400 tabular-nums">{{ String(index + 1).padStart(2, "0") }}</span>
            </div>

            <div class="lg:col-span-6">
                <div class="flex items-center gap-3 mb-3">
                    <LucideIcon :name="service.icon" class="w-5 h-5 text-brand-600 shrink-0" />
                    <h3 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight transition-colors group-hover:text-brand-600">
                        {{ service.name }}
                    </h3>
                </div>
                <p class="text-gray-500 leading-relaxed mb-4 max-w-md">{{ service.short_description }}</p>
                <ul class="text-sm text-gray-600 space-y-1.5 mb-5">
                    <li v-for="feature in service.features?.slice(0, 3)" :key="feature">- {{ feature }}</li>
                </ul>
                <Link :href="'/' + service.slug" class="inline-flex items-center gap-1.5 text-brand-600 text-sm font-semibold">
                    Pelajari lebih lanjut
                    <span aria-hidden="true" class="transition-transform duration-200 group-hover:translate-x-1">-&gt;</span>
                </Link>
            </div>

            <div class="lg:col-span-5">
                <div class="aspect-[16/10] rounded-lg overflow-hidden bg-gray-100">
                    <img
                        :src="service.image || PLACEHOLDER"
                        :alt="service.image ? service.name : ''"
                        :aria-hidden="!service.image"
                        width="640"
                        height="400"
                        loading="lazy"
                        class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                    />
                </div>
            </div>
        </div>
    </div>
</template>
