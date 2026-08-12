<script setup>
/**
 * Full pricing block for the /harga page: category tabs + 3-tier card grid
 * + per-tier renewal note. Homepage uses the lighter PricingCategoryGrid
 * instead — this component is /harga-only.
 */
import { computed, ref } from "vue";
import PricingCard from "./PricingCard.vue";
import { PRICING_CATEGORIES, PRICING_TIERS, TIER_ORDER } from "@/constants/pricing";

const props = defineProps({
    packages: { type: Array, default: () => [] },
    title: { type: String, default: "" },
    subtitle: { type: String, default: "" },
    // Pre-selects a tab from the ?kategori= query param (e.g. arriving via
    // a "Selengkapnya" link from the homepage category grid).
    initialCategory: { type: String, default: "" },
});

const groupedByCategory = computed(() => {
    const map = {};
    for (const cat of PRICING_CATEGORIES) map[cat.value] = [];
    for (const pkg of props.packages) {
        if (map[pkg.category]) map[pkg.category].push(pkg);
    }
    for (const key in map) {
        map[key].sort((a, b) => TIER_ORDER[a.tier] - TIER_ORDER[b.tier]);
    }
    return map;
});

const firstNonEmptyCategory = computed(
    () => PRICING_CATEGORIES.find((cat) => groupedByCategory.value[cat.value].length)?.value || PRICING_CATEGORIES[0].value
);

const isValidInitialCategory = computed(
    () => props.initialCategory && PRICING_CATEGORIES.some((cat) => cat.value === props.initialCategory)
);

const activeCategory = ref(isValidInitialCategory.value ? props.initialCategory : firstNonEmptyCategory.value);
const activePackages = computed(() => groupedByCategory.value[activeCategory.value] || []);

function tierLabel(tier) {
    return PRICING_TIERS.find((t) => t.value === tier)?.label || tier;
}

function formatPrice(price) {
    return "Rp " + new Intl.NumberFormat("id-ID").format(price);
}
</script>

<template>
    <section class="bg-gray-50 py-20">
        <div class="max-w-7xl mx-auto px-4">
            <div v-if="title" v-scroll-reveal class="text-center max-w-2xl mx-auto mb-10">
                <p v-if="subtitle" class="text-brand-600 font-semibold text-sm uppercase tracking-wide mb-2">{{ subtitle }}</p>
                <h2 class="text-3xl font-bold text-gray-900">{{ title }}</h2>
            </div>

            <div class="flex flex-wrap justify-center gap-2 mb-12">
                <button
                    v-for="cat in PRICING_CATEGORIES"
                    :key="cat.value"
                    type="button"
                    @click="activeCategory = cat.value"
                    :class="[
                        'px-5 py-2 rounded-full text-sm font-medium transition-colors',
                        activeCategory === cat.value
                            ? 'bg-brand-600 text-white'
                            : 'bg-white text-gray-600 border border-gray-200 hover:border-brand-300',
                    ]"
                >
                    {{ cat.label }}
                </button>
            </div>

            <div v-if="activePackages.length" class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-6 items-start pt-4">
                <PricingCard
                    v-for="(pkg, index) in activePackages"
                    :key="pkg.id"
                    :pkg="pkg"
                    v-scroll-reveal="{ delay: index * 80 }"
                />
            </div>
            <p v-else class="text-center text-gray-400 py-12">Paket untuk kategori ini akan segera hadir.</p>

            <div v-if="activePackages.length" v-scroll-reveal class="mt-14 bg-white border border-black/10 rounded-2xl p-6 md:p-8">
                <h3 class="text-sm font-semibold text-gray-900 mb-4 uppercase tracking-wide">Harga Renewal per Tier</h3>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div
                        v-for="pkg in activePackages"
                        :key="pkg.id"
                        class="flex items-center justify-between bg-gray-50 rounded-lg px-4 py-3"
                    >
                        <span class="text-sm font-medium text-gray-700">{{ tierLabel(pkg.tier) }}</span>
                        <span class="text-sm font-semibold text-gray-900">
                            {{ pkg.renewal_price ? formatPrice(pkg.renewal_price) + "/tahun" : "-" }}
                        </span>
                    </div>
                </div>
                <p class="text-xs text-gray-500">
                    Sudah termasuk domain + hosting tahun pertama pada harga paket di atas. Renewal berlaku mulai tahun kedua.
                </p>
            </div>
        </div>
    </section>
</template>
