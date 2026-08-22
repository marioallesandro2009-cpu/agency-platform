<script setup>
/**
 * Shared CTA button for all public pages. Standardizes the padding/casing/
 * hover-lift treatment that had drifted per-page (px-6 py-3 vs px-7 py-3.5
 * vs px-6 py-2.5, some missing uppercase/tracking/hover-lift entirely) and
 * adds a real focus-visible ring, which no inline button had.
 *
 * Renders as an Inertia <Link> when `href` is set, a plain <a> when `href`
 * + `external` are both set (e.g. wa.me links), or a <button> otherwise.
 */
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    href: { type: String, default: null },
    external: { type: Boolean, default: false },
    type: { type: String, default: "button" },
    variant: { type: String, default: "primary" }, // primary | outline | ghost
    size: { type: String, default: "default" }, // default | large
    disabled: { type: Boolean, default: false },
});

const tag = computed(() => {
    if (props.href && props.external) return "a";
    if (props.href) return Link;
    return "button";
});

const classes = computed(() => {
    const base =
        "inline-flex items-center justify-center gap-1.5 transition-all duration-200 " +
        "focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 " +
        "disabled:opacity-60 disabled:pointer-events-none";

    if (props.variant === "ghost") {
        return `${base} text-brand-600 hover:text-brand-700 text-sm font-semibold`;
    }

    const size = props.size === "large" ? "px-7 py-3.5" : "px-6 py-3";
    const shape = `${base} ${size} text-sm font-semibold uppercase tracking-wide rounded-md hover:-translate-y-0.5 active:scale-[0.98] active:translate-y-0 disabled:hover:translate-y-0 disabled:active:scale-100`;

    if (props.variant === "outline") {
        return `${shape} border border-white/30 text-white hover:border-white`;
    }

    return `${shape} bg-brand-600 text-white hover:bg-brand-700`;
});
</script>

<template>
    <component
        :is="tag"
        :href="href || undefined"
        :type="tag === 'button' ? type : undefined"
        :target="external ? '_blank' : undefined"
        :rel="external ? 'noopener' : undefined"
        :disabled="tag === 'button' ? disabled : undefined"
        :class="classes"
    >
        <slot />
    </component>
</template>
