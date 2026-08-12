// Project scroll-reveal standard — composable form.
//
// For most cases, prefer the v-scroll-reveal directive instead
// (resources/js/directives/scrollReveal.js) — it needs no script setup:
//   <div v-scroll-reveal>...</div>
//   <div v-for="(item, i) in items" v-scroll-reveal="{ delay: i * 80 }">...</div>
//
// Reach for this composable instead when you need to read `isVisible` /
// `style` from script — e.g. to sequence a hand-built animation, or to
// choreograph two elements against each other (see AboutSection.vue, where
// the image and the text card reveal with a relative delay between them).
//
// Options: { direction: 'up'|'down'|'left'|'right'|'none', duration, delay,
//            distance, threshold, rootMargin, once }
// Returns: { target, isVisible, style } — bind target as a template ref,
// then either use `style` directly (:style="style") or read `isVisible`
// to drive your own classes.

import { computed, onMounted, onUnmounted, ref } from "vue";
import {
    DEFAULT_DISTANCE,
    DEFAULT_DURATION,
    DEFAULT_ROOT_MARGIN,
    DEFAULT_THRESHOLD,
    prefersReducedMotion,
    transformFor,
    transitionFor,
} from "./scrollRevealCore";

export function useScrollReveal(options = {}) {
    const {
        direction = "up",
        duration = DEFAULT_DURATION,
        delay = 0,
        distance = DEFAULT_DISTANCE,
        threshold = DEFAULT_THRESHOLD,
        rootMargin = DEFAULT_ROOT_MARGIN,
        once = true,
    } = options;

    const target = ref(null);
    const isVisible = ref(false);
    const reduced = prefersReducedMotion();

    let observer = null;

    onMounted(() => {
        if (!target.value || reduced) {
            isVisible.value = true;
            return;
        }

        observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    isVisible.value = true;
                    if (once) observer.disconnect();
                } else if (!once) {
                    isVisible.value = false;
                }
            },
            { threshold, rootMargin }
        );

        observer.observe(target.value);
    });

    onUnmounted(() => observer?.disconnect());

    const style = computed(() => {
        if (reduced) return {};

        return {
            opacity: isVisible.value ? 1 : 0,
            transform: isVisible.value ? "translate(0, 0)" : transformFor(direction, distance),
            ...transitionFor(duration, delay),
        };
    });

    return { target, isVisible, style };
}
