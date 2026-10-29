<script setup>
/**
 * StarField — Inertia wrapper around the framework-free starfield in
 * resources/js/space/starfield.js (the Blade pages mount that module
 * directly, without Vue).
 */
import { ref, onMounted, onBeforeUnmount } from "vue";
import { mountStarField } from "@/space/starfield";

const canvasEl = ref(null);
let destroy = null;

onMounted(() => {
    destroy = mountStarField(canvasEl.value);
});
onBeforeUnmount(() => destroy?.());
</script>

<template>
    <canvas ref="canvasEl" class="fixed inset-0 w-full h-full pointer-events-none" aria-hidden="true"></canvas>
</template>
