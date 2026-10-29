<script setup>
/**
 * Multi-step progress indicator (space theme): horizontal row (top) on
 * mobile, vertical sidebar (left) on desktop. `currentStep` is 1-indexed; a
 * step is "done" once the wizard has moved past it (going back doesn't
 * un-complete it — the visitor sees their progress isn't lost). Steps keep
 * their number in every state — no check marks; "done" is shown by the
 * filled lime node and the lit connector instead.
 */
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

const nodeClasses = {
    done: "bg-brand-400 border-brand-400 text-space-void",
    active: "bg-space-deep border-brand-400 text-brand-300 shadow-[0_0_24px_-4px_rgb(var(--lime-400)/0.6)]",
    upcoming: "bg-space-void border-white/15 text-white/40",
};

const labelClasses = {
    done: "text-white/70",
    active: "text-white",
    upcoming: "text-white/35",
};

const lineClass = (index) => (state(index) === "done" ? "bg-brand-400" : "bg-white/10");
const pad = (n) => String(n).padStart(2, "0");
</script>

<template>
    <!-- Mobile: horizontal, top -->
    <ol class="flex md:hidden mb-10" aria-label="Progress pengisian form">
        <li v-for="(step, i) in steps" :key="step" :class="i < steps.length - 1 ? 'flex-1' : ''" class="flex flex-col" :aria-current="state(i) === 'active' ? 'step' : undefined">
            <div class="flex items-center">
                <span
                    class="w-8 h-8 rounded-full border flex items-center justify-center font-mono text-[10px] flex-shrink-0 transition-all"
                    :class="nodeClasses[state(i)]"
                >{{ pad(i + 1) }}</span>
                <div v-if="i < steps.length - 1" class="flex-1 h-px mx-1.5" :class="lineClass(i)" />
            </div>
            <span class="block font-mono text-[9px] uppercase tracking-[0.15em] leading-tight mt-2 max-w-[72px]" :class="labelClasses[state(i)]">{{ step }}</span>
        </li>
    </ol>

    <!-- Desktop: vertical, sidebar -->
    <ol class="hidden md:block" aria-label="Progress pengisian form">
        <li v-for="(step, i) in steps" :key="step" class="flex items-start" :aria-current="state(i) === 'active' ? 'step' : undefined">
            <div class="flex flex-col items-center mr-4">
                <span
                    class="w-9 h-9 rounded-full border flex items-center justify-center font-mono text-[11px] flex-shrink-0 transition-all"
                    :class="nodeClasses[state(i)]"
                >{{ pad(i + 1) }}</span>
                <div v-if="i < steps.length - 1" class="w-px flex-1 min-h-[32px]" :class="lineClass(i)" />
            </div>
            <span class="pt-2.5 pb-8 font-mono text-[10px] uppercase tracking-[0.2em]" :class="labelClasses[state(i)]">{{ step }}</span>
        </li>
    </ol>
</template>
