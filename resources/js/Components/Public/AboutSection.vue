<script setup>
import { Link, usePage } from "@inertiajs/vue3";

defineProps({
    about: Object,
});

const mascots = usePage().props.mascots || {};
</script>

<template>
    <section class="bg-white py-24 md:py-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-12 lg:items-center">
                <div v-scroll-reveal="{ direction: 'left' }" class="lg:col-span-7">
                    <div class="rounded-lg overflow-hidden aspect-[4/3] lg:aspect-[3/4] bg-gray-100">
                        <img v-if="about.image" :src="about.image" :alt="about.title" width="700" height="875" loading="lazy" class="w-full h-full object-cover" />
                    </div>
                </div>

                <div
                    v-scroll-reveal="{ direction: 'right', delay: 150 }"
                    class="lg:col-span-6 relative z-10 bg-white rounded-lg shadow-elevated p-8 md:p-12 -mt-10 mx-4 lg:mx-0 lg:mt-0 lg:-ml-24"
                >
                    <h2 class="text-4xl md:text-5xl font-extrabold tracking-tight text-gray-900 mb-5 leading-[1.05] text-left [text-wrap:balance]">
                        {{ about.title }}
                    </h2>
                    <p class="text-gray-600 text-left leading-relaxed mb-8 max-w-md">
                        {{ about.description }}
                    </p>
                    <Link
                        v-if="about.button_text"
                        :href="about.button_url || '/tentang-kami'"
                        class="inline-block bg-brand-600 text-white text-sm font-semibold uppercase tracking-wide px-7 py-3.5 rounded-md transition-all duration-200 hover:bg-brand-700 hover:-translate-y-0.5"
                    >
                        {{ about.button_text }}
                    </Link>
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
            </div>
        </div>
    </section>
</template>
