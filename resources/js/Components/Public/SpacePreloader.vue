<script setup>
/**
 * SpacePreloader — "000 → 100" counter intro for the space theme. Shown
 * once per browser session only (sessionStorage), skipped entirely under
 * prefers-reduced-motion, and kept short (~1.2s) so it never meaningfully
 * delays the page: content renders underneath the whole time.
 */
import { onMounted, ref } from "vue";
import { prefersReducedMotion } from "@/composables/scrollRevealCore";

const SEEN_KEY = "spacePreloaderSeen";
const DURATION = 1200;

const visible = ref(false);
const leaving = ref(false);
const count = ref(0);

onMounted(() => {
    let seen = false;
    try {
        seen = sessionStorage.getItem(SEEN_KEY) === "1";
        sessionStorage.setItem(SEEN_KEY, "1");
    } catch {
        seen = true;
    }
    if (seen || prefersReducedMotion()) return;

    visible.value = true;
    const start = performance.now();
    const tick = (now) => {
        const p = Math.min((now - start) / DURATION, 1);
        count.value = Math.round(100 * (1 - Math.pow(1 - p, 3)));
        if (p < 1) {
            requestAnimationFrame(tick);
        } else {
            leaving.value = true;
            setTimeout(() => (visible.value = false), 700);
        }
    };
    requestAnimationFrame(tick);
});
</script>

<template>
    <div
        v-if="visible"
        class="preloader fixed inset-0 z-[100] bg-space-void text-white flex flex-col justify-end p-6 md:p-12"
        :class="{ 'preloader--leave': leaving }"
        aria-hidden="true"
    >
        <div class="preloader__grid absolute inset-0 bg-grid-subtle opacity-60"></div>
        <p class="relative font-display font-light text-[22vw] md:text-[14vw] leading-none tabular-nums tracking-tight">
            {{ String(count).padStart(3, "0") }}
        </p>
        <div class="relative mt-4 flex items-center gap-4 font-mono text-[10px] uppercase tracking-[0.25em] text-white/50">
            <span>Initializing system</span>
            <span class="h-px flex-1 bg-white/15 overflow-hidden">
                <span class="block h-full bg-brand-400" :style="{ width: count + '%' }"></span>
            </span>
            <span>{{ count }}%</span>
        </div>
    </div>
</template>

<style scoped>
.preloader {
    transition: transform 0.7s cubic-bezier(0.76, 0, 0.24, 1), opacity 0.7s ease;
}
.preloader--leave {
    transform: translateY(-100%);
}
</style>
