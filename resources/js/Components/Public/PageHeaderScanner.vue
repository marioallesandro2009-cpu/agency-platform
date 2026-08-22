<script setup>
/**
 * Reusable page-title header used on every public page EXCEPT the homepage
 * hero (which keeps its own dedicated markup in Home.vue). Renders an
 * animated Scanner WebGL background behind a big, left-aligned title block.
 *
 * Layering (kept deliberately flat — no `filter`/`backdrop-filter` on any
 * element shared by the canvas and the text, which previously caused a
 * dithering artifact on the homepage hero):
 *   1. .page-header__bg   — solid charcoal fallback (visible before the
 *                            canvas mounts, and wherever the shader is
 *                            fully transparent)
 *   2. ScannerBackground   — absolute canvas layer, z-0
 *   3. .page-header__scrim — thin dark gradient overlay, z-10, for contrast
 *   4. .page-header__content — breadcrumb (optional) + eyebrow/title/
 *                              subtitle text, z-20
 *
 * The section itself carries `isolate` — without it, `position: relative`
 * alone does NOT create a stacking context, so the scrim's z-10 competes
 * with siblings outside this component instead of staying contained. That
 * let the scrim bleed onto any element pulled up over this section's own
 * box with a negative margin (e.g. Portfolio/Show.vue's cover image,
 * `-mt-8`) — z-index:auto siblings still lose to an uncontained z-10, even
 * though `overflow-hidden` keeps the scrim inside this section's own box.
 * `isolate` fixes that at the source, so no caller needs its own workaround.
 */
import ScannerBackground from "./ScannerBackground.vue";
import Breadcrumbs from "./Breadcrumbs.vue";

defineProps({
    eyebrow: { type: String, default: "" },
    title: { type: String, required: true },
    subtitle: { type: String, default: "" },
    color1: { type: String, default: "#4d7c0f" },
    color2: { type: String, default: "#a3e635" },
    color3: { type: String, default: "#1a1a1a" },
    // Optional [{ label, href }] — when provided, renders the breadcrumb
    // trail inside the hero itself (dark variant), above the eyebrow/title,
    // instead of the caller placing a separate <Breadcrumbs> bar on white
    // above this section.
    breadcrumbItems: { type: Array, default: null },
});
</script>

<template>
    <section class="page-header relative isolate overflow-hidden bg-charcoal text-white py-14 sm:py-20 md:py-28">
        <ScannerBackground class="z-0" :color1="color1" :color2="color2" :color3="color3" />

        <div class="page-header__scrim absolute inset-0 z-10" aria-hidden="true"></div>

        <div class="page-header__content relative z-20 max-w-7xl mx-auto px-4">
            <Breadcrumbs v-if="breadcrumbItems?.length" variant="dark" :items="breadcrumbItems" />
            <div class="max-w-3xl">
                <p
                    v-if="eyebrow"
                    v-scroll-reveal
                    class="text-brand-400 font-semibold text-xs uppercase tracking-widest mb-4"
                >
                    {{ eyebrow }}
                </p>
                <h1
                    v-scroll-reveal="{ delay: 80 }"
                    class="text-4xl sm:text-5xl md:text-6xl lg:text-7xl font-extrabold leading-[1.05] tracking-tight mb-4 sm:mb-6 text-left [text-wrap:balance]"
                >
                    {{ title }}
                </h1>
                <p
                    v-if="subtitle"
                    v-scroll-reveal="{ delay: 160 }"
                    class="text-gray-300 text-lg md:text-xl text-left max-w-2xl"
                >
                    {{ subtitle }}
                </p>
                <div v-if="$slots.default" v-scroll-reveal="{ delay: 240 }" class="text-left mt-6">
                    <slot />
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
.page-header__scrim {
    background: linear-gradient(115deg, rgba(0, 0, 0, 0.55) 0%, rgba(0, 0, 0, 0.35) 45%, rgba(0, 0, 0, 0.15) 100%);
}
</style>
