<template>
    <div aria-hidden="true" class="dotted-pattern" :style="patternStyle"></div>
</template>

<script setup>
import { computed } from "vue";

const props = defineProps({
    size: { type: Number, default: 10 },
    color: { type: String, default: "rgba(22, 101, 52, 0.08)" },
});

const patternStyle = computed(() => ({
    backgroundImage: `radial-gradient(circle, ${props.color} 1px, transparent 1px)`,
    backgroundSize: `${props.size}px ${props.size}px`,
}));
</script>

<style scoped>
.dotted-pattern {
    position: absolute;
    inset: 0;
    pointer-events: none;
    z-index: 0;
}
</style>
