<script setup>
/**
 * Homepage pricing teaser: 1 card per category showing its cheapest (Seed)
 * price, linking through to the full tabbed pricing UI on /harga with the
 * category pre-selected via ?kategori=. Deliberately lighter than the full
 * tier-card grid on /harga itself — see PricingSection.vue for that.
 */
import { computed } from "vue";
import Button from "./Button.vue";
import DottedPattern from "@/Components/DottedPattern.vue";
import { Rocket, Building2, ShoppingCart, Database } from "lucide-vue-next";
import { PRICING_CATEGORIES } from "@/constants/pricing";

const props = defineProps({
    packages: { type: Array, default: () => [] },
    title: { type: String, default: "" },
    subtitle: { type: String, default: "" },
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
    <section class="relative bg-gray-100 py-20 md:py-24">
        <DottedPattern color="rgba(77, 124, 15, 0.08)" />
        <div class="relative z-10 max-w-7xl mx-auto px-4">
            <div v-if="title" v-scroll-reveal class="text-center max-w-2xl mx-auto mb-14">
                <p v-if="subtitle" class="text-brand-600 font-semibold text-xs uppercase tracking-widest mb-2">{{ subtitle }}</p>
                <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-gray-900">{{ title }}</h2>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    v-for="(card, index) in cards"
                    :key="card.value"
                    v-scroll-reveal="{ delay: index * 80 }"
                    class="bg-white rounded-lg border border-black/10 p-6 flex flex-col transition-colors duration-200 hover:border-brand-300"
                >
                    <div class="w-10 h-10 rounded-md bg-brand-50 text-brand-700 flex items-center justify-center mb-4">
                        <component :is="card.icon" class="w-5 h-5" />
                    </div>
                    <h3 class="font-bold tracking-tight text-gray-900 mb-3">{{ card.label }}</h3>
                    <div class="mb-5 flex-1">
                        <p class="text-xs text-gray-500">Mulai dari</p>
                        <p class="text-xl font-bold text-gray-900">
                            {{ card.cheapestPrice ? formatPrice(card.cheapestPrice) : "Segera Hadir" }}
                        </p>
                    </div>
                    <Button variant="ghost" :href="`/harga?kategori=${card.value}`">
                        Selengkapnya <span aria-hidden="true">&rarr;</span>
                    </Button>
                </div>
            </div>
        </div>
    </section>
</template>
