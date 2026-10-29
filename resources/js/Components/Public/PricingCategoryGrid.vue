<script setup>
/**
 * Homepage pricing teaser: 1 card per category showing its cheapest (Seed)
 * price, linking through to the full tabbed pricing UI on /harga with the
 * category pre-selected via ?kategori=. Deliberately lighter than the full
 * tier-card grid on /harga itself — see PricingSection.vue for that.
 */
import { computed } from "vue";
import { Link } from "@inertiajs/vue3";
import SpaceSectionHeading from "./SpaceSectionHeading.vue";
import { Rocket, Building2, ShoppingCart, Database } from "lucide-vue-next";
import { PRICING_CATEGORIES } from "@/constants/pricing";

const props = defineProps({
    packages: { type: Array, default: () => [] },
    title: { type: String, default: "" },
    subtitle: { type: String, default: "" },
    index: { type: Number, default: null }, // HUD section number on the space-theme homepage
});

const CATEGORY_ICONS = {
    landing_page: Rocket,
    company_profile: Building2,
    ecommerce: ShoppingCart,
    sistem_informasi: Database,
};

const cards = computed(() =>
    PRICING_CATEGORIES.map((cat) => {
        const catPackages = props.packages.filter((pkg) => pkg.category === cat.value);
        const cheapest = catPackages.reduce((min, pkg) => (pkg.price && (!min || pkg.price < min) ? pkg.price : min), null);

        return {
            ...cat,
            icon: CATEGORY_ICONS[cat.value],
            cheapestPrice: cheapest,
        };
    })
);

function formatPrice(price) {
    return "Rp " + new Intl.NumberFormat("id-ID").format(price);
}
</script>

<template>
    <section class="relative py-24 md:py-32">
        <div class="relative z-10 max-w-[1440px] mx-auto px-4 md:px-8">
            <SpaceSectionHeading v-if="title" :index="index" :label="subtitle" :title="title" />

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-px rounded-2xl overflow-hidden border border-white/10 bg-white/10">
                <Link
                    v-for="(card, i) in cards"
                    :key="card.value"
                    v-scroll-reveal="{ delay: i * 80 }"
                    :href="`/harga?kategori=${card.value}`"
                    class="group relative bg-space-void p-7 md:p-8 flex flex-col min-h-[300px] transition-colors duration-300 hover:bg-space-deep focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-brand-400"
                >
                    <div class="flex items-center justify-between mb-auto">
                        <component :is="card.icon" class="w-6 h-6 text-white/50 transition-colors group-hover:text-brand-400" aria-hidden="true" />
                        <span class="font-mono text-[10px] tracking-[0.2em] text-white/30">/{{ String(i + 1).padStart(2, "0") }}</span>
                    </div>
                    <h3 class="font-display font-semibold text-xl text-white tracking-tight mt-12 mb-4">{{ card.label }}</h3>
                    <p class="font-mono text-[10px] uppercase tracking-[0.2em] text-white/40 mb-1">Mulai dari</p>
                    <p class="font-display text-2xl text-brand-300 tabular-nums mb-6">
                        {{ card.cheapestPrice ? formatPrice(card.cheapestPrice) : "Segera Hadir" }}
                    </p>
                    <span class="font-mono text-[11px] uppercase tracking-[0.18em] text-white/60 group-hover:text-white flex items-center gap-2">
                        Selengkapnya <span aria-hidden="true" class="transition-transform duration-300 group-hover:translate-x-1">&rarr;</span>
                    </span>
                    <span class="absolute inset-x-0 bottom-0 h-px bg-brand-400 origin-left scale-x-0 transition-transform duration-500 group-hover:scale-x-100" aria-hidden="true"></span>
                </Link>
            </div>
        </div>
    </section>
</template>
