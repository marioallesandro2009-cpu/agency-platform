// useParallax — very subtle scroll-linked vertical offset for large hero-
// scale images. Deliberately minimal by design (see the brief this shipped
// for: "±10-20px, not a template animation showcase") — capped at
// MAX_OFFSET, driven by requestAnimationFrame off a passive scroll
// listener (never a scroll-jank-prone synchronous handler), and disabled
// entirely under prefers-reduced-motion or below the lg breakpoint (brief:
// parallax should be off or near-off on mobile).
//
//   const { target, offset } = useParallax();
//   <div ref="target" :style="{ transform: `translateY(${offset}px)` }">
//
// Options: { strength } — how much of the element's distance from viewport
// center translates to offset, default 0.15 (tuned to land within the
// ±16px cap for typical hero-image scroll distances without feeling like
// it's fighting the cap constantly).

import { onMounted, onUnmounted, ref } from "vue";
import { prefersReducedMotion } from "./scrollRevealCore";

const MAX_OFFSET = 16;
const MIN_VIEWPORT_WIDTH = 1024;

export function useParallax({ strength = 0.15 } = {}) {
    const target = ref(null);
    const offset = ref(0);
    const reduced = prefersReducedMotion();

    let rafId = null;
    let enabled = false;

    function clamp(value) {
        return Math.max(-MAX_OFFSET, Math.min(MAX_OFFSET, value));
    }

    function update() {
        rafId = null;
        if (!target.value) return;
        const rect = target.value.getBoundingClientRect();
        const viewportCenter = window.innerHeight / 2;
        const elementCenter = rect.top + rect.height / 2;
        offset.value = clamp(-(elementCenter - viewportCenter) * strength);
    }

    function onScroll() {
        if (rafId) return;
        rafId = requestAnimationFrame(update);
    }

    function checkEnabled() {
        enabled = !reduced && window.innerWidth >= MIN_VIEWPORT_WIDTH;
        if (!enabled) offset.value = 0;
    }

    onMounted(() => {
        checkEnabled();
        if (!enabled) return;
        update();
        window.addEventListener("scroll", onScroll, { passive: true });
        window.addEventListener("resize", checkEnabled, { passive: true });
    });

    onUnmounted(() => {
        window.removeEventListener("scroll", onScroll);
        window.removeEventListener("resize", checkEnabled);
        if (rafId) cancelAnimationFrame(rafId);
    });

    return { target, offset };
}
