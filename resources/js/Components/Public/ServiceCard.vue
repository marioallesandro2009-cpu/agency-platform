<script setup>
/**
 * Service row: numbered-list treatment (01 - Service Name) with an
 * alternating full-bleed background per row and a bordered/shadowed card
 * around the image+text block, so each item reads as a distinct grouped
 * unit instead of floating in shared whitespace. The full-bleed background
 * uses the left-1/2 + -translate-x-1/2 + w-screen trick, which only centers
 * correctly because every ancestor up to <body> is itself centered via
 * mx-auto with symmetric padding - don't add an asymmetric wrapper above
 * this component without re-checking that.
 *
 * Text/image sides alternate by index parity (order-1/order-2 swap) at the
 * lg breakpoint so the section doesn't read as the same layout repeated N
 * times. On mobile both sides collapse to image-first/text-second
 * regardless of parity, since a left/right swap has no meaning in a single
 * stacked column.
 */
import Button from "./Button.vue";
import LucideIcon from "./LucideIcon.vue";
import { Check } from "lucide-vue-next";

defineProps({
    service: { type: Object, required: true },
    index: { type: Number, default: 0 },
});

const PLACEHOLDER = "https://placehold.co/640x400?text=%20";
</script>

<template>
    <div class="group relative">
        <div
            class="absolute inset-y-0 left-1/2 w-screen -translate-x-1/2 -z-10"
            :class="[index % 2 === 1 ? 'bg-gray-50' : 'bg-white', index > 0 ? 'border-t border-gray-100' : '']"
            aria-hidden="true"
        ></div>

        <div class="py-8 md:py-10">
            <div class="rounded-xl border border-gray-100 bg-white shadow-sm p-6 md:p-8">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">
                    <div class="lg:col-span-6 order-2" :class="index % 2 === 1 ? 'lg:order-2' : 'lg:order-1'">
                        <p
                            v-if="service.badge_label"
                            class="inline-block text-[11px] font-semibold uppercase tracking-widest text-brand-700 bg-brand-50 px-2.5 py-1 rounded-full mb-3"
                        >
                            {{ service.badge_label }}
                        </p>
                        <span class="block text-4xl md:text-5xl font-extrabold text-gray-200 leading-none mb-3 tabular-nums" aria-hidden="true">
                            {{ String(index + 1).padStart(2, "0") }}
                        </span>
                        <div class="flex items-center gap-3 mb-3">
                            <LucideIcon :name="service.icon" class="w-5 h-5 text-brand-600 shrink-0" />
                            <h3 class="text-2xl md:text-3xl font-bold text-gray-900 tracking-tight transition-colors group-hover:text-brand-600">
                                {{ service.name }}
                            </h3>
                        </div>
                        <p class="text-gray-500 leading-relaxed mb-4 max-w-md">{{ service.short_description }}</p>
                        <ul class="text-sm text-gray-600 space-y-1.5 mb-5">
                            <li v-for="feature in service.features?.slice(0, 3)" :key="feature" class="flex items-start gap-2">
                                <Check class="w-4 h-4 text-brand-600 shrink-0 mt-0.5" aria-hidden="true" />
                                <span>{{ feature }}</span>
                            </li>
                        </ul>
                        <Button variant="ghost" :href="'/' + service.slug">
                            Pelajari lebih lanjut
                            <span aria-hidden="true" class="transition-transform duration-200 group-hover:translate-x-1">-&gt;</span>
                        </Button>
                    </div>

                    <div class="lg:col-span-6 order-1" :class="index % 2 === 1 ? 'lg:order-1' : 'lg:order-2'">
                        <div v-image-reveal class="aspect-[16/10] rounded-lg overflow-hidden shadow-soft bg-gray-100">
                            <img
                                :src="service.image || PLACEHOLDER"
                                :alt="service.image ? service.name : ''"
                                :aria-hidden="!service.image"
                                width="640"
                                height="400"
                                loading="lazy"
                                class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
