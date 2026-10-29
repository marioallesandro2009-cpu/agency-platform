<script setup>
/**
 * Service row for the dark "space" homepage: a glass panel with a giant
 * outlined index numeral, the service's image in a framed "viewport" with
 * HUD corner ticks, and a cursor-following lime glow on hover. Text/image
 * sides alternate by index parity at the lg breakpoint so the list doesn't
 * read as one layout repeated N times; on mobile both collapse to
 * image-first/text-second regardless of parity.
 */
import { ref } from "vue";
import Button from "./Button.vue";
import LucideIcon from "./LucideIcon.vue";

defineProps({
    service: { type: Object, required: true },
    index: { type: Number, default: 0 },
});

const PLACEHOLDER = "https://placehold.co/640x400/0b0d14/0b0d14?text=%20";

const card = ref(null);
function trackGlow(e) {
    const rect = card.value.getBoundingClientRect();
    card.value.style.setProperty("--gx", `${e.clientX - rect.left}px`);
    card.value.style.setProperty("--gy", `${e.clientY - rect.top}px`);
}
</script>

<template>
    <div class="py-4 md:py-5">
        <div
            ref="card"
            class="service-card group relative overflow-hidden rounded-2xl border border-white/10 bg-space-deep/60 backdrop-blur-md p-6 md:p-10 transition-colors duration-300 hover:border-brand-400/40"
            @pointermove="trackGlow"
        >
            <div class="service-card__glow pointer-events-none absolute inset-0 opacity-0 transition-opacity duration-300 group-hover:opacity-100" aria-hidden="true"></div>

            <div class="relative grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-center">
                <div class="lg:col-span-6 order-2" :class="index % 2 === 1 ? 'lg:order-2' : 'lg:order-1'">
                    <div class="flex items-start justify-between gap-4 mb-6">
                        <span
                            class="font-display font-extrabold text-6xl md:text-7xl leading-none tabular-nums text-transparent [-webkit-text-stroke:1px_rgb(var(--ink)/0.22)] transition-all duration-500 group-hover:[-webkit-text-stroke:1px_rgb(var(--lime-400)/0.8)]"
                            aria-hidden="true"
                        >
                            {{ String(index + 1).padStart(2, "0") }}
                        </span>
                        <p
                            v-if="service.badge_label"
                            class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-300 border border-brand-400/30 bg-brand-400/10 px-2.5 py-1 rounded-full"
                        >
                            {{ service.badge_label }}
                        </p>
                    </div>
                    <div class="flex items-center gap-3 mb-4">
                        <LucideIcon :name="service.icon" class="w-5 h-5 text-brand-400 shrink-0" />
                        <h3 class="font-display font-semibold text-2xl md:text-3xl text-white tracking-tight">
                            {{ service.name }}
                        </h3>
                    </div>
                    <p class="text-white/55 leading-relaxed mb-5 max-w-md">{{ service.short_description }}</p>
                    <ul class="text-sm text-white/70 space-y-2 mb-7">
                        <li v-for="feature in service.features?.slice(0, 3)" :key="feature" class="flex items-start gap-3">
                            <span class="w-3 h-px bg-brand-400 shrink-0 mt-[0.7rem]" aria-hidden="true"></span>
                            <span>{{ feature }}</span>
                        </li>
                    </ul>
                    <Button variant="link" :href="'/' + service.slug">
                        Pelajari lebih lanjut
                        <span aria-hidden="true" class="transition-transform duration-200 group-hover:translate-x-1">&rarr;</span>
                    </Button>
                </div>

                <div class="lg:col-span-6 order-1" :class="index % 2 === 1 ? 'lg:order-1' : 'lg:order-2'">
                    <div class="hud-frame relative p-2">
                        <div v-image-reveal class="aspect-[16/10] rounded-lg overflow-hidden bg-space-panel">
                            <img
                                :src="service.image || PLACEHOLDER"
                                :alt="service.image ? service.name : ''"
                                :aria-hidden="!service.image"
                                width="640"
                                height="400"
                                loading="lazy"
                                class="w-full h-full object-cover opacity-90 transition-all duration-700 group-hover:scale-105 group-hover:opacity-100"
                            />
                        </div>
                        <span class="absolute -bottom-5 right-2 font-mono text-[9px] uppercase tracking-[0.2em] text-white/30" aria-hidden="true">
                            SVC-{{ String(index + 1).padStart(3, "0") }} / {{ service.slug }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.service-card__glow {
    background: radial-gradient(420px circle at var(--gx, 50%) var(--gy, 50%), rgb(var(--lime-400) / 0.09), transparent 60%);
}

/* HUD corner ticks around the image viewport */
.hud-frame {
    background:
        linear-gradient(#a3e635, #a3e635) top left / 14px 1px no-repeat,
        linear-gradient(#a3e635, #a3e635) top left / 1px 14px no-repeat,
        linear-gradient(#a3e635, #a3e635) top right / 14px 1px no-repeat,
        linear-gradient(#a3e635, #a3e635) top right / 1px 14px no-repeat,
        linear-gradient(#a3e635, #a3e635) bottom left / 14px 1px no-repeat,
        linear-gradient(#a3e635, #a3e635) bottom left / 1px 14px no-repeat,
        linear-gradient(#a3e635, #a3e635) bottom right / 14px 1px no-repeat,
        linear-gradient(#a3e635, #a3e635) bottom right / 1px 14px no-repeat;
}
</style>
