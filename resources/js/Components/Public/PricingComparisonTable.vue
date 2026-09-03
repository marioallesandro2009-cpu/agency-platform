<script setup>
/**
 * Feature-by-feature comparison table for the active category's 3 tiers on
 * /harga. Rows come from each package's admin-editable `comparison_features`
 * (array of {feature, value}) — the union of feature labels across the
 * category's packages, in the order they first appear (Seed, then Growth,
 * then Scale introduces any new row). A tier missing a given row renders a
 * dash rather than being excluded, so the table always stays rectangular.
 */
import { computed } from "vue";
import { Minus } from "lucide-vue-next";
import { PRICING_TIERS } from "@/constants/pricing";

const props = defineProps({
    packages: { type: Array, default: () => [] },
});

function tierLabel(tier) {
    return PRICING_TIERS.find((t) => t.value === tier)?.label || tier;
}

const rows = computed(() => {
    const labels = [];
    for (const pkg of props.packages) {
        for (const row of pkg.comparison_features || []) {
            if (row.feature && !labels.includes(row.feature)) labels.push(row.feature);
        }
    }
    return labels;
});

function valueFor(pkg, label) {
    const row = (pkg.comparison_features || []).find((r) => r.feature === label);
    return row?.value?.trim() || null;
}
</script>

<template>
    <div v-if="rows.length" v-scroll-reveal class="mt-8 bg-white border border-black/10 rounded-lg overflow-hidden">
        <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide px-6 pt-6">Perbandingan Fitur per Tier</h3>
        <div class="overflow-x-auto mt-4">
            <table class="w-full text-sm min-w-[480px]">
                <thead>
                    <tr class="border-b border-black/10">
                        <th class="text-left font-medium text-gray-500 px-6 py-3 w-2/5">Fitur</th>
                        <th
                            v-for="pkg in packages"
                            :key="pkg.id"
                            :class="['text-center font-semibold px-4 py-3', pkg.tier === 'growth' ? 'text-brand-700 bg-brand-50' : 'text-gray-700']"
                        >
                            {{ tierLabel(pkg.tier) }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="label in rows" :key="label" class="border-b border-black/5 last:border-b-0">
                        <td class="px-6 py-3 text-gray-700">{{ label }}</td>
                        <td
                            v-for="pkg in packages"
                            :key="pkg.id"
                            :class="['text-center px-4 py-3', pkg.tier === 'growth' ? 'bg-brand-50/60 font-medium text-gray-900' : 'text-gray-600']"
                        >
                            <span v-if="valueFor(pkg, label) && valueFor(pkg, label) !== '-'">{{ valueFor(pkg, label) }}</span>
                            <Minus v-else class="w-3.5 h-3.5 text-gray-300 mx-auto" aria-label="Tidak tersedia" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
