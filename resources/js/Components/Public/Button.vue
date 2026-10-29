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
    variant: { type: String, default: "primary" }, // primary | outline | ghost | pill | glass | link (last three: dark "space" theme)
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

    // Space-theme variants: rounded pills on near-black, mono-cased labels.
    if (props.variant === "link") {
        return `${base} focus-visible:ring-offset-space-void text-brand-300 hover:text-brand-200 font-mono text-xs uppercase tracking-[0.18em]`;
    }
    if (props.variant === "pill" || props.variant === "glass") {
        const pad = props.size === "large" ? "pl-6 pr-1.5 py-1.5" : "pl-5 pr-1 py-1";
        const look =
            props.variant === "pill"
                ? "bg-white text-space-void hover:bg-brand-300"
                : "border border-white/20 bg-white/5 backdrop-blur-md text-white hover:border-white/50 hover:bg-white/10";
        return `${base} focus-visible:ring-offset-space-void group/btn ${pad} gap-3 rounded-full text-sm font-semibold ${look} active:scale-[0.98]`;
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
        <span
            v-if="variant === 'pill' || variant === 'glass'"
            aria-hidden="true"
            class="inline-flex items-center justify-center rounded-full transition-transform duration-300 group-hover/btn:rotate-[-45deg]"
            :class="[
                size === 'large' ? 'w-10 h-10' : 'w-8 h-8',
                variant === 'pill' ? 'bg-brand-400 text-space-void' : 'bg-white/10 text-white',
            ]"
        >&rarr;</span>
    </component>
</template>
