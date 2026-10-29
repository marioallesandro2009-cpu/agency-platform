<script setup>
/**
 * StarField — lightweight fixed 2D-canvas backdrop for the "space" theme
 * (PublicLayout theme="space"). A few hundred twinkling points that drift
 * with scroll for a little depth; the heavy WebGL work is reserved for the
 * hero (Planet.vue). Static single frame under prefers-reduced-motion,
 * and the loop pauses while the tab is hidden.
 */
import { ref, onMounted, onBeforeUnmount } from "vue";
import { prefersReducedMotion } from "@/composables/scrollRevealCore";

const canvasEl = ref(null);
let ctx = null;
let stars = [];
let rafId = null;
let w = 0;
let h = 0;
let dpr = 1;
const reducedMotion = prefersReducedMotion();

function seed() {
    const count = Math.round((w * h) / 6500);
    stars = Array.from({ length: count }, () => ({
        x: Math.random() * w,
        y: Math.random() * h,
        r: Math.random() * 1.1 + 0.2,
        depth: Math.random() * 0.6 + 0.1,
        phase: Math.random() * Math.PI * 2,
        speed: Math.random() * 1.5 + 0.5,
        warm: Math.random() < 0.18,
    }));
}

function resize() {
    dpr = Math.min(window.devicePixelRatio || 1, 2);
    w = window.innerWidth;
    h = window.innerHeight;
    canvasEl.value.width = w * dpr;
    canvasEl.value.height = h * dpr;
    ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
    seed();
    if (reducedMotion) draw(0);
}

// Light theme: stars become faint ink "dust" on the paper background instead
// of pale points that would vanish on it.
function isLight() {
    return document.documentElement.getAttribute("data-theme") === "light";
}

function draw(t) {
    ctx.clearRect(0, 0, w, h);
    const scroll = window.scrollY || 0;
    const light = isLight();
    for (const s of stars) {
        const y = (((s.y - scroll * s.depth * 0.15) % h) + h) % h;
        const a = 0.35 + 0.65 * (0.5 + 0.5 * Math.sin(t * 0.001 * s.speed + s.phase));
        if (light) {
            ctx.fillStyle = s.warm ? `rgba(77, 124, 15, ${a * 0.55})` : `rgba(14, 16, 22, ${a * 0.35})`;
        } else {
            ctx.fillStyle = s.warm ? `rgba(255, 214, 170, ${a})` : `rgba(220, 230, 255, ${a})`;
        }
        ctx.beginPath();
        ctx.arc(s.x, y, s.r, 0, Math.PI * 2);
        ctx.fill();
    }
}

function loop(t) {
    draw(t);
    rafId = requestAnimationFrame(loop);
}

function onVisibility() {
    if (document.hidden) {
        cancelAnimationFrame(rafId);
        rafId = null;
    } else if (!reducedMotion && !rafId) {
        rafId = requestAnimationFrame(loop);
    }
}

onMounted(() => {
    ctx = canvasEl.value.getContext("2d");
    if (!ctx) return;
    resize();
    window.addEventListener("resize", resize);
    document.addEventListener("visibilitychange", onVisibility);
    if (!reducedMotion) rafId = requestAnimationFrame(loop);
});

onBeforeUnmount(() => {
    cancelAnimationFrame(rafId);
    window.removeEventListener("resize", resize);
    document.removeEventListener("visibilitychange", onVisibility);
});
</script>

<template>
    <canvas ref="canvasEl" class="fixed inset-0 w-full h-full pointer-events-none" aria-hidden="true"></canvas>
</template>
