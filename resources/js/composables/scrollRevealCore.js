// Shared primitives behind the project's scroll-reveal standard.
// Both useScrollReveal.js (composable) and directives/scrollReveal.js
// (v-scroll-reveal) build on these so the two APIs stay visually identical.

export const DEFAULT_DURATION = 600; // ms, within the 500-700ms house range
export const DEFAULT_EASING = "cubic-bezier(0.16, 1, 0.3, 1)"; // smooth ease-out
export const DEFAULT_THRESHOLD = 0.2;
export const DEFAULT_ROOT_MARGIN = "0px 0px -10% 0px";
export const DEFAULT_DISTANCE = 28; // px offset for the slide

const OFFSETS = {
    up: (d) => [0, d],
    down: (d) => [0, -d],
    left: (d) => [-d, 0],
    right: (d) => [d, 0],
    none: () => [0, 0],
};

export function offsetFor(direction = "up", distance = DEFAULT_DISTANCE) {
    const fn = OFFSETS[direction] || OFFSETS.up;
    return fn(distance);
}

export function transformFor(direction, distance) {
    const [x, y] = offsetFor(direction, distance);
    return `translate(${x}px, ${y}px)`;
}

export function prefersReducedMotion() {
    return typeof window !== "undefined" && window.matchMedia
        ? window.matchMedia("(prefers-reduced-motion: reduce)").matches
        : false;
}

export function transitionFor(duration = DEFAULT_DURATION, delay = 0) {
    return {
        transition: `opacity ${duration}ms ${DEFAULT_EASING}, transform ${duration}ms ${DEFAULT_EASING}`,
        transitionDelay: `${delay}ms`,
    };
}
