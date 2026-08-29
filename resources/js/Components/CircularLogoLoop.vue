<template>
    <div ref="rootRef" class="circular-logo-loop" role="region" :aria-label="ariaLabel" :style="{ height: size + 'px' }">
        <div
            v-for="(item, index) in loopItems"
            :key="index"
            class="logo-wrapper"
            :style="{ width: size + 'px', height: size + 'px' }"
        >
            <component
                :is="item.href ? 'a' : 'div'"
                v-bind="item.href ? { href: item.href, target: '_blank', rel: 'noreferrer noopener' } : {}"
                class="logo"
                :aria-label="item.title"
            >
                <img :src="item.src" :alt="item.alt || ''" :title="item.title" loading="lazy" draggable="false" />
            </component>
        </div>
    </div>
</template>

<script setup>
/**
 * Bare-logo marquee, adapted from a GSAP CodePen demo (originally circular
 * badges positioned with xPercent + gsap.utils.wrap for a seamless infinite
 * loop). Ported to plain pixel-based `x` tweening instead of `xPercent` so a
 * `gap` between logos is just an added constant — xPercent is relative to
 * each element's own width, which has no room for extra spacing.
 *
 * No card/circle frame and no edge-fade overlay: logos render as their own
 * PNG/SVG on the page background, exactly as supplied.
 *
 * The source demo only animates when there are more logos than fit on
 * screen at once (`length > 5`); with a short real logo list that guard
 * would leave the strip static, so the list is repeated until there are
 * enough items for the wrap-based loop to read as continuous motion.
 */
import { ref, computed, onMounted, onBeforeUnmount, watch, nextTick } from "vue";
import { gsap } from "gsap";

const props = defineProps({
    logos: { type: Array, required: true },
    size: { type: Number, default: 72 },
    gap: { type: Number, default: 56 },
    // Seconds of travel per single slot (size + gap) — lower is faster.
    speedPerItem: { type: Number, default: 1.4 },
    ariaLabel: { type: String, default: "Partner logos" },
});

const MIN_ITEMS = 10;

const loopItems = computed(() => {
    if (!props.logos.length) return [];
    const repeats = Math.max(1, Math.ceil(MIN_ITEMS / props.logos.length));
    return Array.from({ length: repeats }).flatMap(() => props.logos);
});

const rootRef = ref(null);
let tween = null;

function prefersReducedMotion() {
    return window.matchMedia("(prefers-reduced-motion: reduce)").matches;
}

function teardown() {
    tween?.kill();
    tween = null;
}

async function setup() {
    teardown();
    await nextTick();

    const wrappers = rootRef.value?.querySelectorAll(".logo-wrapper");
    if (!wrappers?.length) return;

    const slot = props.size + props.gap;

    gsap.set(wrappers, { autoAlpha: 1 });
    wrappers.forEach((el, i) => gsap.set(el, { x: slot * i }));

    if (prefersReducedMotion()) return;

    const wrap = gsap.utils.wrap(-slot, (wrappers.length - 1) * slot);
    tween = gsap.to(wrappers, {
        x: `-=${wrappers.length * slot}`,
        duration: wrappers.length * props.speedPerItem,
        repeat: -1,
        ease: "none",
        modifiers: {
            x: (x) => wrap(parseFloat(x)) + "px",
        },
    });
}

onMounted(setup);
onBeforeUnmount(teardown);
watch(() => props.logos, setup);
</script>

<style scoped>
.circular-logo-loop {
    position: relative;
    width: 100%;
    overflow: hidden;
}

.logo-wrapper {
    position: absolute;
    top: 0;
    left: 0;
    visibility: hidden;
}

.logo {
    display: grid;
    place-content: center;
    width: 100%;
    height: 100%;
}

.logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    pointer-events: none;
    -webkit-user-drag: none;
}

@media (prefers-reduced-motion: reduce) {
    .logo-wrapper {
        visibility: visible !important;
    }
}
</style>
