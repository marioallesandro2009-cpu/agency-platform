<script setup>
/**
 * Service card: image header (16:9, placeholder fallback), numbered
 * watermark, green border-left accent, icon in a soft-tint circle, hover
 * lift. Unlike WhyUsCard, description/features stay always visible here —
 * there's already an image + "Pelajari lebih lanjut" CTA doing the work,
 * so hover-hiding the copy would hurt rather than help scannability.
 */
import { Link } from "@inertiajs/vue3";
import LucideIcon from "./LucideIcon.vue";

defineProps({
    service: { type: Object, required: true },
    index: { type: Number, default: 0 },
});

const PLACEHOLDER = "https://placehold.co/640x360?text=%20";
</script>

<template>
    <div class="service-card group relative bg-white rounded-xl shadow-sm overflow-hidden border-l-4 border-brand-600 flex flex-col h-full">
        <div class="aspect-[16/9] bg-gray-100 overflow-hidden">
            <img
                :src="service.image || PLACEHOLDER"
                :alt="service.image ? service.name : ''"
                :aria-hidden="!service.image"
                class="w-full h-full object-cover service-card__img"
                loading="lazy"
            />
        </div>

        <div class="relative p-6 flex flex-col flex-1">
            <span class="service-card__number" aria-hidden="true">{{ String(index + 1).padStart(2, "0") }}</span>

            <div class="service-card__icon relative z-10 w-12 h-12 rounded-full bg-brand-50 flex items-center justify-center mb-4">
                <LucideIcon :name="service.icon" class="w-6 h-6 text-brand-600" />
            </div>

            <h3 class="relative z-10 font-semibold text-lg text-gray-900 mb-2">{{ service.name }}</h3>
            <p class="relative z-10 text-sm text-gray-500 mb-4">{{ service.short_description }}</p>
            <ul class="relative z-10 text-xs text-gray-600 space-y-1 mb-4">
                <li v-for="feature in service.features?.slice(0, 3)" :key="feature">- {{ feature }}</li>
            </ul>

            <Link :href="'/' + service.slug" class="relative z-10 mt-auto text-brand-600 text-sm font-medium hover:underline">
                Pelajari lebih lanjut
            </Link>
        </div>
    </div>
</template>

<style scoped>
.service-card__number {
    position: absolute;
    top: 0.5rem;
    right: 1rem;
    font-size: 3rem;
    line-height: 1;
    font-weight: 800;
    color: #4d7c0f;
    opacity: 0.12;
    pointer-events: none;
    user-select: none;
}
@media (min-width: 768px) {
    .service-card__number {
        font-size: 3.5rem;
    }
}

.service-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
}
.service-card__icon {
    transition: transform 0.3s ease;
}
.service-card__img {
    transition: transform 0.3s ease;
}

@media (hover: hover) and (pointer: fine) {
    .service-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        border-color: #84cc16;
    }
    .service-card:hover .service-card__icon {
        transform: scale(1.1);
    }
    .service-card:hover .service-card__img {
        transform: scale(1.05);
    }
}

@media (prefers-reduced-motion: reduce) {
    .service-card,
    .service-card__icon,
    .service-card__img {
        transition: none;
    }
}

.service-card:focus-within {
    outline: 2px solid #84cc16;
    outline-offset: 2px;
}
</style>
