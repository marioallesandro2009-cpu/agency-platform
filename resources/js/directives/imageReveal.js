// v-image-reveal — like v-scroll-reveal, but scale(1.04) -> scale(1)
// instead of a translate offset, tuned for images specifically ("this
// makes an image feel premium" — scroll-reveal's directional slide reads
// oddly on a photo, a gentle settle-into-place scale reads as intentional).
// Requires the element (or its immediate wrapper) to have overflow-hidden
// so the initial 1.04 scale doesn't visibly overflow its rounded corners —
// every current use site already has that via its image-wrapper div.
//
//   <img v-image-reveal class="w-full h-full object-cover" />
//   <img v-image-reveal="{ delay: 100 }" ... />
//
// Options: { delay } — ms, default 0. Duration is fixed at 900ms (the
// project's 800-1000ms house range for image reveals).

import { DEFAULT_EASING, prefersReducedMotion } from "../composables/scrollRevealCore";

const DURATION = 900;
const THRESHOLD = 0.2;
const ROOT_MARGIN = "0px 0px -10% 0px";

function applyHiddenState(el) {
    el.style.opacity = "0";
    el.style.transform = "scale(1.04)";
}

function applyRevealedState(el, delay) {
    el.style.transition = `opacity ${DURATION}ms ${DEFAULT_EASING}, transform ${DURATION}ms ${DEFAULT_EASING}`;
    el.style.transitionDelay = `${delay}ms`;
    el.style.opacity = "1";
    el.style.transform = "scale(1)";
}

export const vImageReveal = {
    mounted(el, binding) {
        const delay = binding.value?.delay ?? 0;

        if (prefersReducedMotion()) return;

        applyHiddenState(el);

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    applyRevealedState(el, delay);
                    observer.disconnect();
                }
            },
            { threshold: THRESHOLD, rootMargin: ROOT_MARGIN }
        );

        observer.observe(el);
        el.__imageRevealObserver = observer;
    },

    unmounted(el) {
        el.__imageRevealObserver?.disconnect();
        delete el.__imageRevealObserver;
    },
};
