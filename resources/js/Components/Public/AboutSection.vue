<script setup>
import { usePage } from "@inertiajs/vue3";
import Button from "./Button.vue";
import { useParallax } from "@/composables/useParallax";

defineProps({
    about: Object,
    index: { type: Number, default: null }, // HUD section number on the space-theme homepage
});

const mascots = usePage().props.mascots || {};
const { target: coverImageTarget, offset: coverImageOffset } = useParallax();
</script>

<template>
    <section class="py-24 md:py-36 overflow-hidden">
        <div class="max-w-[1440px] mx-auto px-4 md:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 lg:items-center gap-8 lg:gap-0">
                <div
                    v-scroll-reveal="{ direction: 'right', delay: 150 }"
                    class="lg:col-start-6 lg:col-span-7 lg:row-start-1 relative z-10 rounded-2xl border border-white/10 bg-space-deep/70 backdrop-blur-xl p-8 md:p-12 mx-4 lg:mx-0 shadow-[0_40px_80px_-30px_rgba(0,0,0,0.8)]"
                >
                    <div class="flex items-center gap-4 font-mono text-[10px] uppercase tracking-[0.25em] text-white/45 mb-6">
                        <span v-if="index !== null" class="text-brand-400">[ {{ String(index).padStart(2, "0") }} ]</span>
                        <span>Tentang kami</span>
                        <span class="h-px flex-1 bg-gradient-to-r from-white/20 to-transparent"></span>
                    </div>
                    <h2 class="font-display font-semibold text-3xl md:text-5xl tracking-tight text-white mb-6 leading-[1.05] text-left [text-wrap:balance]">
                        {{ about.title }}
                    </h2>
                    <p class="text-white/60 text-left leading-relaxed mb-8">
                        {{ about.description }}
                    </p>
                    <Button v-if="about.button_text" :href="about.button_url || '/tentang-kami'" variant="pill" size="large">
                        {{ about.button_text }}
                    </Button>
                    <p v-if="about.extra_note" class="font-mono text-xs text-white/40 mt-5">{{ about.extra_note }}</p>

                    <img
                        v-if="mascots.kenalan_dekat_kami?.image_path"
                        :src="mascots.kenalan_dekat_kami.image_path"
                        :alt="mascots.kenalan_dekat_kami.alt_text || ''"
                        width="220"
                        height="220"
                        loading="lazy"
                        class="w-full max-w-[180px] mt-8 object-contain"
                    />
                </div>

                <div class="lg:col-start-1 lg:col-span-7 lg:row-start-1">
                    <div v-image-reveal class="relative rounded-2xl overflow-hidden aspect-[4/3] lg:aspect-[3/4] bg-space-panel ring-1 ring-white/10">
                        <img
                            v-if="about.image"
                            ref="coverImageTarget"
                            :src="about.image"
                            :alt="about.title"
                            width="700"
                            height="875"
                            loading="lazy"
                            class="w-full h-full object-cover"
                            :style="{ transform: `translateY(${coverImageOffset}px) scale(1.1)` }"
                        />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
