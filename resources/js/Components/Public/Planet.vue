<script setup>
/**
 * Planet — Inertia wrapper around the framework-free planet hero in
 * resources/js/space/planet.js (the Blade homepage mounts that module
 * directly, without Vue). The container's CSS gradient is the fallback
 * look when WebGL is unavailable.
 */
import { ref, onMounted, onBeforeUnmount } from "vue";
import { mountPlanet } from "@/space/planet";

const props = defineProps({
    accentColor: { type: Array, default: undefined },
    center: { type: Array, default: undefined },
    centerMobile: { type: Array, default: undefined },
    scale: { type: Number, default: undefined },
});

const container = ref(null);
let destroy = null;

onMounted(() => {
    const options = Object.fromEntries(Object.entries(props).filter(([, v]) => v !== undefined));
    destroy = mountPlanet(container.value, options);
});
onBeforeUnmount(() => destroy?.());
</script>

<template>
    <div ref="container" class="planet-bg" aria-hidden="true"></div>
</template>
