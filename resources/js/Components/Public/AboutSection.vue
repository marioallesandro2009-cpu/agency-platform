<script setup>
import { usePage } from "@inertiajs/vue3";
import Button from "./Button.vue";

defineProps({
    about: Object,
});

const mascots = usePage().props.mascots || {};
</script>

<template>
    <section class="bg-gray-50 py-24 md:py-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 lg:items-center gap-8 lg:gap-0">
                <div
                    v-scroll-reveal="{ direction: 'right', delay: 150 }"
                    class="lg:col-start-6 lg:col-span-7 lg:row-start-1 relative z-10 bg-white rounded-lg shadow-elevated p-8 md:p-12 mx-4 lg:mx-0"
                >
                    <h2 class="text-4xl md:text-5xl font-extrabold tracking-tight text-gray-900 mb-5 leading-[1.05] text-left [text-wrap:balance]">
                        {{ about.title }}
                    </h2>
                    <p class="text-gray-600 text-left leading-relaxed mb-8">
                        {{ about.description }}
                    </p>
                    <Button v-if="about.button_text" :href="about.button_url || '/tentang-kami'" size="large">
                        {{ about.button_text }}
                    </Button>
                    <p v-if="about.extra_note" class="text-sm text-gray-500 mt-4">{{ about.extra_note }}</p>

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

                <div v-scroll-reveal="{ direction: 'left' }" class="lg:col-start-1 lg:col-span-7 lg:row-start-1">
                    <div class="rounded-xl overflow-hidden aspect-[4/3] lg:aspect-[3/4] bg-gray-100">
                        <img v-if="about.image" :src="about.image" :alt="about.title" width="700" height="875" loading="lazy" class="w-full h-full object-cover" />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
