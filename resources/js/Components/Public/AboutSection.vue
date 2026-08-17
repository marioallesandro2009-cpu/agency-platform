<script setup>
import { Link, usePage } from "@inertiajs/vue3";

defineProps({
    about: Object,
});

const mascots = usePage().props.mascots || {};
</script>

<template>
    <section class="bg-white py-20 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-5 lg:items-center">
                <div v-scroll-reveal="{ direction: 'left' }" class="lg:col-span-2">
                    <div class="rounded-2xl overflow-hidden shadow-md aspect-[4/5] bg-gray-100">
                        <img v-if="about.image" :src="about.image" :alt="about.title" class="w-full h-full object-cover" />
                    </div>
                </div>

                <div
                    v-scroll-reveal="{ direction: 'right', delay: 150 }"
                    class="lg:col-span-3 relative z-10 bg-white rounded-2xl shadow-xl border border-gray-100 p-8 md:p-10 mt-8 lg:mt-0 lg:-ml-16"
                >
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 leading-tight text-left">
                        {{ about.title }}
                    </h2>
                    <p class="text-gray-600 text-left leading-relaxed mb-8">
                        {{ about.description }}
                    </p>
                    <Link
                        v-if="about.button_text"
                        :href="about.button_url || '/tentang-kami'"
                        class="inline-block border-2 border-brand-600 text-brand-600 font-medium px-6 py-3 rounded-md transition-all duration-300 ease-out hover:bg-brand-600 hover:text-white hover:scale-[1.03]"
                    >
                        {{ about.button_text }}
                    </Link>
                    <p v-if="about.extra_note" class="text-sm text-gray-500 mt-4">{{ about.extra_note }}</p>

                    <img
                        v-if="mascots.kenalan_dekat_kami?.image_path"
                        :src="mascots.kenalan_dekat_kami.image_path"
                        :alt="mascots.kenalan_dekat_kami.alt_text || ''"
                        class="w-full max-w-xs mt-8 object-contain"
                    />
                </div>
            </div>
        </div>
    </section>
</template>
