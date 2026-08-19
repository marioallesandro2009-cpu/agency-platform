<script setup>
/**
 * Multi-step progress indicator: horizontal row (top) on mobile, vertical
 * sidebar (left) on desktop. `currentStep` is 1-indexed; a step is "done"
 * once the wizard has moved past it (going back doesn't un-complete it —
 * the whole point is the visitor sees their progress isn't lost).
 */
import { Check } from "lucide-vue-next";

const props = defineProps({
    steps: { type: Array, required: true },
    currentStep: { type: Number, required: true },
});

function state(index) {
    const stepNumber = index + 1;
    if (stepNumber < props.currentStep) return "done";
    if (stepNumber === props.currentStep) return "active";
    return "upcoming";
}

const circleClasses = {
    done: "bg-brand-600 border-brand-600 text-white",
    active: "bg-white border-brand-600 text-brand-600",
    upcoming: "bg-white border-gray-300 text-gray-500",
};

const labelClasses = {
    done: "text-gray-700",
    active: "text-gray-900 font-semibold",
    upcoming: "text-gray-500",
};

const lineClass = (index) => (state(index) === "done" ? "bg-brand-600" : "bg-gray-200");
</script>

<template>
    <!-- Mobile: horizontal, top -->
    <ol class="flex md:hidden mb-8" aria-label="Progress pengisian form">
        <li v-for="(step, i) in steps" :key="step" :class="i < steps.length - 1 ? 'flex-1' : ''" class="flex flex-col">
            <div class="flex items-center">
                <span
                    class="w-7 h-7 rounded-full border-2 flex items-center justify-center text-xs font-semibold flex-shrink-0"
                    :class="circleClasses[state(i)]"
                >
                    <Check v-if="state(i) === 'done'" class="w-3.5 h-3.5" />
                    <template v-else>{{ i + 1 }}</template>
                </span>
                <div v-if="i < steps.length - 1" class="flex-1 h-0.5 mx-1" :class="lineClass(i)" />
            </div>
            <span class="block text-[10px] leading-tight mt-1 max-w-[72px]" :class="labelClasses[state(i)]">{{ step }}</span>
        </li>
    </ol>

    <!-- Desktop: vertical, sidebar -->
    <ol class="hidden md:block" aria-label="Progress pengisian form">
        <li v-for="(step, i) in steps" :key="step" class="flex items-start">
            <div class="flex flex-col items-center mr-3">
                <span
                    class="w-8 h-8 rounded-full border-2 flex items-center justify-center text-sm font-semibold flex-shrink-0"
                    :class="circleClasses[state(i)]"
                >
                    <Check v-if="state(i) === 'done'" class="w-4 h-4" />
                    <template v-else>{{ i + 1 }}</template>
                </span>
                <div v-if="i < steps.length - 1" class="w-0.5 flex-1 min-h-[24px]" :class="lineClass(i)" />
            </div>
            <span class="pt-1.5 pb-6 text-sm" :class="labelClasses[state(i)]">{{ step }}</span>
        </li>
    </ol>
</template>
