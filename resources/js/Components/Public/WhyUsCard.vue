<script setup>
/**
 * "Why Us" card: numbered watermark, green border-left accent, icon in a
 * soft-tint circle, hover lift, and — unlike ServiceCard — the description
 * is hover-revealed (icon + title only by default) since this section's
 * copy tends to run longer. See the .why-us-card__desc rules below for how
 * that stays accessible: it's hidden via max-height/opacity (never
 * display:none), it also opens on :focus-within for keyboard users, and
 * `@media (hover: none)` (touch devices, no real hover) forces it always
 * visible — otherwise the text would be permanently unreachable by tap.
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
    <div tabindex="0" class="why-us-card group relative bg-white rounded-xl shadow-sm p-8 overflow-hidden border-l-4 border-brand-600">
        <span class="why-us-card__number" aria-hidden="true">{{ String(index + 1).padStart(2, "0") }}</span>

        <div class="why-us-card__icon relative z-10 w-16 h-16 rounded-full flex items-center justify-center mb-5">
            <LucideIcon :name="icon" class="w-8 h-8 text-brand-600" />
        </div>

        <h3 class="relative z-10 font-semibold text-xl text-gray-900 mb-2">{{ title }}</h3>

        <div class="why-us-card__desc relative z-10">
            <p class="text-base text-gray-500 leading-relaxed">{{ description }}</p>
        </div>
    </div>
</template>

<style scoped>
.why-us-card__number {
    position: absolute;
    top: 0.5rem;
    right: 1.25rem;
    font-size: 3.5rem;
    line-height: 1;
    font-weight: 800;
    color: #4d7c0f;
    opacity: 0.06;
    pointer-events: none;
    user-select: none;
}
@media (min-width: 768px) {
    .why-us-card__number {
        font-size: 4.5rem;
    }
}

.why-us-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
}

.why-us-card__icon {
    background: linear-gradient(135deg, #ecfccb 0%, #d9f99d 100%);
    box-shadow: 0 4px 12px -2px rgb(77 124 15 / 0.25), inset 0 1px 0 rgb(255 255 255 / 0.6);
}

.why-us-card__desc {
    max-height: 0;
    opacity: 0;
    overflow: hidden;
    transition: max-height 0.35s ease, opacity 0.3s ease, margin-top 0.35s ease;
}

/* Hover/lift/reveal effects only for devices with a real hover-capable
   pointer (mouse/trackpad) — on touch, :hover can "stick" after a tap and
   there's no way to un-hover, so we skip it entirely there (see the
   `(hover: none)` block) and just show everything, per the mobile fallback
   requirement. */
@media (hover: hover) and (pointer: fine) {
    .why-us-card:hover,
    .why-us-card:focus-within {
        transform: translateY(-6px);
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        border-color: #84cc16;
    }
    .why-us-card:hover .why-us-card__icon,
    .why-us-card:focus-within .why-us-card__icon {
        transform: scale(1.1);
    }
    .why-us-card:hover .why-us-card__desc,
    .why-us-card:focus-within .why-us-card__desc {
        max-height: 260px;
        opacity: 1;
        margin-top: 0.5rem;
    }
}

.why-us-card__icon {
    transition: transform 0.3s ease;
}

/* Touch / no real hover: always show the description, never rely on :hover. */
@media (hover: none) {
    .why-us-card__desc {
        max-height: none;
        opacity: 1;
        overflow: visible;
        margin-top: 0.375rem;
    }
}

@media (prefers-reduced-motion: reduce) {
    .why-us-card,
    .why-us-card__icon,
    .why-us-card__desc {
        transition: none;
    }
}

.why-us-card:focus-visible {
    outline: 2px solid #84cc16;
    outline-offset: 2px;
}
</style>
