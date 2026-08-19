<script setup>
/**
 * Ported from a React + GSAP accordion gallery: horizontal panels where the
 * active one is wide and in color, the rest are narrow and grayscale, with a
 * smooth flex-grow/tilt/parallax transition on hover or click. Vue Composition
 * API equivalent of useRef/useEffect/useCallback is ref/onMounted/watch below.
 */
import { onMounted, onUnmounted, ref, watch } from "vue";
import { router } from "@inertiajs/vue3";
import { gsap } from "gsap";

const props = defineProps({
    items: { type: Array, default: () => [] },
    defaultIndex: { type: Number, default: 0 },
    accentColor: { type: String, default: "#4d7c0f" },
    overlayColor: { type: String, default: "#1a1a1a" },
    textColor: { type: String, default: "#ffffff" },
    height: { type: Number, default: 420 },
    gap: { type: Number, default: 10 },
    radius: { type: Number, default: 16 },
    expandRatio: { type: Number, default: 0.5 },
    duration: { type: Number, default: 0.6 },
    ease: { type: String, default: "power3.out" },
    parallax: { type: Number, default: 0.3 },
    tilt: { type: Number, default: 6 },
    stagger: { type: Number, default: 0.05 },
    trigger: { type: String, default: "hover" },
    showLabels: { type: Boolean, default: true },
    grayscale: { type: Boolean, default: true },
});

const activeIndex = ref(Math.min(props.defaultIndex, Math.max(props.items.length - 1, 0)));
const rootRef = ref(null);
const isMobile = ref(false);
const panelRefs = ref([]);
const mediaRefs = ref([]);
const labelRefs = ref([]);

let prefersReducedMotion = false;
let mediaQuery = null;
let resizeObserver = null;

function setPanelRef(el, index) {
    panelRefs.value[index] = el;
}
function setMediaRef(el, index) {
    mediaRefs.value[index] = el;
}
function setLabelRef(el, index) {
    labelRefs.value[index] = el;
}

function computeFlexGrow(index) {
    const count = props.items.length;
    if (count <= 1) return 1;
    if (index === activeIndex.value) return props.expandRatio * count * 10;
    const remaining = (1 - props.expandRatio) * count * 10;
    return remaining / (count - 1);
}

function applyLayout(immediate = false) {
    if (isMobile.value || !props.items.length) return;

    const instant = immediate || prefersReducedMotion;

    props.items.forEach((_, index) => {
        const panel = panelRefs.value[index];
        const media = mediaRefs.value[index];
        const label = labelRefs.value[index];
        if (!panel) return;

        const isActive = index === activeIndex.value;
        const offset = index - activeIndex.value;
        const delay = instant ? 0 : Math.abs(offset) * props.stagger;

        gsap.to(panel, {
            flexGrow: computeFlexGrow(index),
            rotateY: isActive ? 0 : Math.sign(offset) * props.tilt,
            duration: instant ? 0 : props.duration,
            ease: props.ease,
            delay,
        });

        if (media) {
            gsap.to(media, {
                xPercent: isActive ? 0 : -offset * props.parallax * 8,
                filter: props.grayscale && !isActive ? "grayscale(1)" : "grayscale(0)",
                duration: instant ? 0 : props.duration,
                ease: props.ease,
                delay,
            });
        }

        if (label) {
            gsap.to(label, {
                opacity: isActive && props.showLabels ? 1 : 0,
                y: isActive ? 0 : 10,
                duration: instant ? 0 : props.duration * 0.75,
                ease: props.ease,
                delay: isActive ? delay + props.duration * 0.25 : 0,
            });
        }
    });
}

function setActive(index) {
    if (index === activeIndex.value) return;
    activeIndex.value = index;
}

function handleEnter(index) {
    if (props.trigger === "hover") setActive(index);
}

function handleActivate(index) {
    if (index !== activeIndex.value) {
        setActive(index);
        return;
    }
    if (props.items[index]?.link) {
        router.visit(props.items[index].link);
    }
}

function handleKeydown(event, index) {
    if (event.key === "Enter" || event.key === " ") {
        event.preventDefault();
        handleActivate(index);
        return;
    }
    if (event.key !== "ArrowLeft" && event.key !== "ArrowRight") return;
    event.preventDefault();
    const delta = event.key === "ArrowRight" ? 1 : -1;
    const next = (activeIndex.value + delta + props.items.length) % props.items.length;
    setActive(next);
    panelRefs.value[next]?.focus();
}

function checkMobile() {
    isMobile.value = window.innerWidth < 520;
}

watch(activeIndex, () => applyLayout());
watch(isMobile, (mobile) => {
    if (!mobile) applyLayout(true);
});

onMounted(() => {
    prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    mediaQuery = window.matchMedia("(prefers-reduced-motion: reduce)");
    mediaQuery.addEventListener?.("change", (event) => {
        prefersReducedMotion = event.matches;
    });

    checkMobile();
    window.addEventListener("resize", checkMobile);

    applyLayout(true);

    if (typeof ResizeObserver !== "undefined" && rootRef.value) {
        resizeObserver = new ResizeObserver(() => applyLayout(true));
        resizeObserver.observe(rootRef.value);
    }
});

onUnmounted(() => {
    window.removeEventListener("resize", checkMobile);
    resizeObserver?.disconnect();
    [...panelRefs.value, ...mediaRefs.value, ...labelRefs.value].forEach((el) => el && gsap.killTweensOf(el));
});
</script>

<template>
    <div
        ref="rootRef"
        class="w-full flex"
        :class="isMobile ? 'flex-col' : 'flex-row'"
        :style="{
            gap: gap + 'px',
            height: isMobile ? 'auto' : height + 'px',
            perspective: isMobile ? 'none' : '1400px',
        }"
        :aria-label="'Galeri portfolio'"
    >
        <div
            v-for="(item, index) in items"
            :key="item.link || item.label || index"
            :ref="(el) => setPanelRef(el, index)"
            role="link"
            :aria-label="item.label"
            :aria-current="index === activeIndex ? 'true' : undefined"
            tabindex="0"
            class="relative overflow-hidden cursor-pointer outline-none focus-visible:ring-2 focus-visible:ring-offset-2"
            :class="isMobile ? 'w-full flex-none' : 'grow shrink basis-0'"
            :style="{
                borderRadius: radius + 'px',
                height: isMobile ? height * 0.6 + 'px' : '100%',
                transformStyle: isMobile ? 'flat' : 'preserve-3d',
                backgroundColor: overlayColor,
                '--tw-ring-color': accentColor,
            }"
            @mouseenter="handleEnter(index)"
            @click="handleActivate(index)"
            @keydown="handleKeydown($event, index)"
        >
            <img
                :ref="(el) => setMediaRef(el, index)"
                :src="item.image"
                :alt="item.label"
                width="400"
                height="500"
                class="absolute inset-0 h-full w-full object-cover"
                loading="lazy"
            />
            <div
                class="absolute inset-0 pointer-events-none"
                :style="{ background: `linear-gradient(180deg, transparent 45%, ${overlayColor} 100%)` }"
            ></div>
            <span
                v-if="item.badge"
                class="absolute left-5 top-5 bg-gray-100 text-gray-700 text-xs font-medium px-3 py-1 rounded-full"
            >
                {{ item.badge }}
            </span>
            <div
                v-if="showLabels"
                :ref="(el) => setLabelRef(el, index)"
                class="absolute left-5 bottom-5 flex items-center gap-3 opacity-0"
                :style="{ color: textColor }"
            >
                <span class="h-8 w-1 rounded-full flex-shrink-0" :style="{ background: accentColor }"></span>
                <span class="font-semibold whitespace-nowrap drop-shadow">{{ item.label }}</span>
            </div>
        </div>
    </div>
</template>
