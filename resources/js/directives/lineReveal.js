// v-line-reveal — thin decorative dividers that "draw" in once, left to
// right, the first time they enter the viewport. Sibling to v-scroll-reveal
// (same IntersectionObserver approach, same house easing), for the
// "section transition motion" case scroll-reveal's translate/opacity isn't
// shaped for: a horizontal rule growing from 0 to full width.
//
//   <div class="h-px bg-gray-200" v-line-reveal></div>
//   <div class="h-px bg-gray-200" v-line-reveal="{ delay: index * 60 }"></div>
//
// Options: { delay } — ms, default 0. Duration/easing are fixed (not
// exposed as options) so every line-draw on the site reads as the same
// motion language; see DURATION below for the reasoning on the value.

import { DEFAULT_EASING, prefersReducedMotion } from "../composables/scrollRevealCore";

// 1500ms sits in the middle of the project's 1200-1800ms house range for
// this specific effect (slower than a content reveal — a line-draw is a
// deliberate editorial flourish, not something that should feel rushed).
const DURATION = 1500;
const THRESHOLD = 0.2;
const ROOT_MARGIN = "0px 0px -10% 0px";

export const vLineReveal = {
    mounted(el, binding) {
        const delay = binding.value?.delay ?? 0;

        if (prefersReducedMotion()) {
            el.style.transform = "scaleX(1)";
            return;
        }

        el.style.transformOrigin = "left";
        el.style.transform = "scaleX(0)";
        el.style.transition = `transform ${DURATION}ms ${DEFAULT_EASING}`;
        el.style.transitionDelay = `${delay}ms`;

        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    el.style.transform = "scaleX(1)";
                    observer.disconnect();
                }
            },
            { threshold: THRESHOLD, rootMargin: ROOT_MARGIN }
        );

        observer.observe(el);
        el.__lineRevealObserver = observer;
    },

    unmounted(el) {
        el.__lineRevealObserver?.disconnect();
        delete el.__lineRevealObserver;
    },
};
