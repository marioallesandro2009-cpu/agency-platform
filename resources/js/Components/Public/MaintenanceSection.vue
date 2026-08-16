<script setup>
/**
 * "Paket Maintenance Lanjutan" — sits below the tier-card renewal note on
 * /harga, category-independent (same 3 plans regardless of which pricing
 * tab is active). Deliberately lighter-weight cards than PricingCard so
 * this stays visually secondary to the Seed/Growth/Scale grid above it.
 */
import { computed, reactive } from "vue";
import { CircleCheck } from "lucide-vue-next";
import { usePage } from "@inertiajs/vue3";

const props = defineProps({
    plans: { type: Array, default: () => [] },
});

const page = usePage();
const waNumber = computed(() => page.props.settings?.contact?.whatsapp_number);

// Per-card billing toggle — each card remembers its own choice, so
// comparing e.g. Basic monthly against Priority yearly is possible.
const billing = reactive({});
function periodFor(plan) {
    return billing[plan.id] || "monthly";
}
function setPeriod(plan, period) {
    billing[plan.id] = period;
}

function formatPrice(price, period) {
    const suffix = period === "yearly" ? "/tahun" : "/bulan";
    return "Rp " + new Intl.NumberFormat("id-ID").format(price) + suffix;
}

// Resolves what to show for a plan's current period: the final price, an
// optional "original" price to strike through, and a badge label.
//
// Yearly has two possible sources for the struck-through price: an explicit
// admin-set original_price_yearly (a real promo), or — when that's absent —
// the plan's own monthly price x 12 (frames the yearly price as "cheaper
// than paying monthly all year"). The explicit admin value wins when both
// are present, since it's a deliberate choice rather than a side effect of
// the monthly price.
function priceDisplay(plan) {
    const period = periodFor(plan);

    if (period === "monthly") {
        const final = plan.price;
        const original = plan.original_price_monthly;
        if (!original || original <= final) return { final, original: null, badge: null };
        const percent = Math.round((1 - final / original) * 100);
        return { final, original, badge: `-${percent}%` };
    }

    const final = plan.price_yearly;
    if (!final) return { final, original: null, badge: null };

    if (plan.original_price_yearly && plan.original_price_yearly > final) {
        const original = plan.original_price_yearly;
        const percent = Math.round((1 - final / original) * 100);
        return { final, original, badge: `-${percent}%` };
    }

    const listPrice = plan.price * 12;
    if (listPrice > final) {
        const percent = Math.round((1 - final / listPrice) * 100);
        const months = Math.round((listPrice - final) / plan.price);
        return { final, original: listPrice, badge: `-${percent}% · Hemat ${months} bulan` };
    }

    return { final, original: null, badge: null };
}

function waLink(planName) {
    const base = waNumber.value ? `https://wa.me/${waNumber.value}` : "/konsultasi";
    if (!waNumber.value) return base;
    return `${base}?text=${encodeURIComponent(`Halo, saya ingin tanya soal paket maintenance ${planName}.`)}`;
}
</script>

<template>
    <section v-if="plans.length" class="py-20">
        <div class="max-w-7xl mx-auto px-4">
            <div v-scroll-reveal class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-2">Paket Maintenance Lanjutan</h2>
                <p class="text-sm text-gray-500">
                    Setelah masa maintenance gratis habis, pilih paket lanjutan sesuai kebutuhan Anda — atau tetap tanpa
                    paket, dengan garansi bug-fixing dasar tetap berlaku.
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto">
                <div
                    v-for="(plan, index) in plans"
                    :key="plan.id"
                    v-scroll-reveal="{ delay: index * 80 }"
                    class="bg-white rounded-xl border border-black/10 p-6 flex flex-col"
                >
                    <h3 class="font-semibold text-gray-900">{{ plan.name }}</h3>

                    <div v-if="plan.price_yearly" class="inline-flex items-center gap-0.5 bg-gray-100 rounded-full p-0.5 mt-3 mb-4 self-start">
                        <button
                            type="button"
                            @click="setPeriod(plan, 'monthly')"
                            :class="periodFor(plan) === 'monthly' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500'"
                            class="text-xs font-medium px-3 py-1 rounded-full transition-colors"
                        >
                            Bulanan
                        </button>
                        <button
                            type="button"
                            @click="setPeriod(plan, 'yearly')"
                            :class="periodFor(plan) === 'yearly' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500'"
                            class="text-xs font-medium px-3 py-1 rounded-full transition-colors"
                        >
                            Tahunan
                        </button>
                    </div>

                    <div class="mb-4">
                        <div class="flex items-baseline gap-2 flex-wrap">
                            <p class="text-lg font-bold text-gray-900">
                                {{ formatPrice(priceDisplay(plan).final, periodFor(plan)) }}
                            </p>
                            <span v-if="priceDisplay(plan).original" class="text-sm text-gray-400 line-through">
                                {{ formatPrice(priceDisplay(plan).original, periodFor(plan)) }}
                            </span>
                        </div>
                        <span
                            v-if="priceDisplay(plan).badge"
                            class="inline-block mt-1 text-[11px] font-medium text-brand-700 bg-brand-50 px-2 py-0.5 rounded-full"
                        >
                            {{ priceDisplay(plan).badge }}
                        </span>
                    </div>

                    <ul class="space-y-2 flex-1 mb-6">
                        <li v-for="feature in plan.features" :key="feature" class="flex items-start gap-2 text-sm text-gray-600">
                            <CircleCheck class="w-4 h-4 text-brand-600 shrink-0 mt-0.5" />
                            <span>{{ feature }}</span>
                        </li>
                    </ul>

                    <a
                        :href="waLink(plan.name)"
                        target="_blank"
                        rel="noopener"
                        class="block text-center text-sm font-medium py-2.5 rounded-full border border-brand-600 text-brand-600 hover:bg-brand-50 transition-colors"
                    >
                        {{ plan.cta_text || "Tanya Lebih Lanjut" }}
                    </a>
                </div>
            </div>

            <p class="text-center text-xs text-gray-400 max-w-2xl mx-auto mt-8">
                Paket maintenance opsional. Website Anda tetap mendapat garansi bug-fixing sesuai ketentuan tier yang
                dipilih, meski tanpa paket maintenance lanjutan.
            </p>
        </div>
    </section>
</template>
