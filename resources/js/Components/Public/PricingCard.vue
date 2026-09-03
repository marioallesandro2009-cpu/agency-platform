<script setup>
/**
 * Single pricing tier card (Seed/Growth/Scale). Header color progresses
 * light -> brand -> dark within the single brand-green palette so the 3
 * tiers read as a visual ladder without introducing off-brand hues. Only
 * renders benefits the package actually includes (features array) — there
 * is no "not included" state by design (see PricingSection.vue).
 */
import { computed } from "vue";
import { CircleCheck, Clock } from "lucide-vue-next";
import Button from "./Button.vue";
import { PRICING_TIERS } from "@/constants/pricing";

const props = defineProps({
    pkg: { type: Object, required: true },
});

const tierLabel = computed(() => PRICING_TIERS.find((t) => t.value === props.pkg.tier)?.label || props.pkg.name);
const orderUrl = computed(() => `/konsultasi?kategori=${props.pkg.category}&tier=${props.pkg.tier}`);

const TIER_STYLES = {
    seed: {
        header: "bg-brand-100",
        pill: "bg-brand-400 text-charcoal",
        price: "text-charcoal",
        prefix: "text-charcoal/60",
        card: "border-black/10",
    },
    growth: {
        header: "bg-brand-600",
        pill: "bg-white text-brand-700",
        price: "text-white",
        prefix: "text-white/80",
        card: "border-brand-700 border-2 shadow-elevated md:-translate-y-2",
    },
    scale: {
        header: "bg-charcoal",
        pill: "bg-brand-700 text-white",
        price: "text-brand-400",
        prefix: "text-white/60",
        card: "border-black/10",
    },
};

const style = computed(() => TIER_STYLES[props.pkg.tier] || TIER_STYLES.seed);
const isGrowth = computed(() => props.pkg.tier === "growth");

function formatPrice(price) {
    if (!price) return null;
    return "Rp " + new Intl.NumberFormat("id-ID").format(price);
}
</script>

<template>
    <div class="relative flex flex-col h-full">
        <span
            v-if="isGrowth"
            class="absolute -top-4 left-1/2 -translate-x-1/2 z-10 bg-brand-700 text-white text-xs font-bold uppercase tracking-widest px-4 py-1.5 rounded-full whitespace-nowrap"
        >
            {{ pkg.badge || "Best Value" }}
        </span>

        <div
            :class="['rounded-lg overflow-hidden border bg-white flex flex-col flex-1 transition-transform duration-300', style.card]"
        >
            <div :class="['px-6 pt-8 pb-6 text-center', style.header]">
                <span :class="['inline-block px-4 py-1 rounded-full text-xs font-bold uppercase tracking-wide', style.pill]">
                    {{ tierLabel }}
                </span>
                <p v-if="pkg.tagline" :class="['mt-2 text-xs italic', style.prefix]">{{ pkg.tagline }}</p>
                <p v-if="pkg.price_prefix" :class="['mt-4 text-xs', style.prefix]">{{ pkg.price_prefix }}</p>
                <p :class="['text-3xl font-bold leading-tight', pkg.price_prefix ? 'mt-1' : 'mt-4', style.price]">
                    {{ pkg.price ? formatPrice(pkg.price) : pkg.price_suffix }}
                </p>
                <p v-if="pkg.price && pkg.price_suffix" :class="['mt-1 text-xs', style.prefix]">{{ pkg.price_suffix }}</p>
                <p v-if="pkg.value_note" :class="['mt-3 text-xs leading-snug', style.prefix]">{{ pkg.value_note }}</p>
                <p v-if="pkg.estimated_days" :class="['mt-1.5 text-xs flex items-center justify-center gap-1', style.prefix]">
                    <Clock class="w-3 h-3 shrink-0" />
                    Selesai dalam {{ pkg.estimated_days }}
                </p>
            </div>

            <div class="p-6 flex flex-col flex-1">
                <ul class="space-y-3 flex-1 mb-6">
                    <li v-for="(feature, index) in pkg.features" :key="index" class="flex items-start gap-2 text-sm text-gray-700">
                        <CircleCheck class="w-5 h-5 text-brand-600 shrink-0 mt-0.5" />
                        <span>
                            {{ feature.label }}
                            <span v-if="feature.note" class="block text-xs text-gray-400 mt-0.5">{{ feature.note }}</span>
                        </span>
                    </li>
                </ul>

                <Button :href="orderUrl" class="w-full">{{ pkg.cta_text || "Pesan Website" }}</Button>
            </div>
        </div>
    </div>
</template>
