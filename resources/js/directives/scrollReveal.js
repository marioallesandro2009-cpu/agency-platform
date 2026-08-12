// v-scroll-reveal — project-wide scroll-reveal standard.
//
// Fades + slides an element in the first time it enters the viewport, once
// only. This is the default way to add scroll-reveal to a section: no
// script setup needed, just attach the directive.
//
// Registered globally in app.js, so it works in every .vue file as-is:
//
//   <h2 v-scroll-reveal>Judul Section</h2>
//
//   <!-- slide in from the left instead of the default (up) -->
//   <div v-scroll-reveal="{ direction: 'left' }">...</div>
//
//   <!-- stagger a list: each item's delay is its own index * 80ms -->
//   <div v-for="(item, i) in items" :key="item.id" v-scroll-reveal="{ delay: i * 80 }">
//     {{ item.title }}
//   </div>
//
// Options (all optional): {
//   direction: 'up' | 'down' | 'left' | 'right' | 'none'  (default 'up')
//   duration:  ms, default 600 (house range is 500-700ms — don't stray far)
//   delay:     ms, default 0 — use `index * 80` (80-100ms) for stagger in v-for
//   distance:  px slide offset, default 28
//   threshold: IntersectionObserver threshold, default 0.2
//   once:      default true — set false only if you have a real reason to replay
// }
//
// Respects prefers-reduced-motion (element just renders in its final state,
// no animation). Needs a real reason to pass `direction`/`duration` that
// differs from the defaults — the whole point is one consistent motion
// language across the site.

import {
    DEFAULT_DISTANCE,
    DEFAULT_DURATION,
    DEFAULT_ROOT_MARGIN,
    DEFAULT_THRESHOLD,
    prefersReducedMotion,
    transformFor,
    transitionFor,
} from "../composables/scrollRevealCore";

function applyHiddenState(el, direction, distance) {
    el.style.opacity = "0";
    el.style.transform = transformFor(direction, distance);
}

function applyRevealedState(el, duration, delay) {
    const { transition, transitionDelay } = transitionFor(duration, delay);
    el.style.transition = transition;
    el.style.transitionDelay = transitionDelay;
    el.style.opacity = "1";
    el.style.transform = "translate(0, 0)";
}

export const vScrollReveal = {
    mounted(el, binding) {
        const options = binding.value || {};
        const direction = options.direction || "up";
        const duration = options.duration ?? DEFAULT_DURATION;
        const delay = options.delay ?? 0;
        const distance = options.distance ?? DEFAULT_DISTANCE;
        const threshold = options.threshold ?? DEFAULT_THRESHOLD;
        const once = options.once ?? true;

        if (prefersReducedMotion()) {
            return;
        }

        applyHiddenState(el, direction, distance);

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    applyRevealedState(el, duration, delay);
                    if (once) observer.disconnect();
                } else if (!once) {
                    applyHiddenState(el, direction, distance);
                }
            },
            { threshold, rootMargin: DEFAULT_ROOT_MARGIN }
        );

        observer.observe(el);
        el.__scrollRevealObserver = observer;
    },

    unmounted(el) {
        el.__scrollRevealObserver?.disconnect();
        delete el.__scrollRevealObserver;
    },
};
