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

function yearlyListPrice(plan) {
    return plan.price * 12;
}

function monthsSaved(plan) {
    if (!plan.price_yearly) return 0;
    return Math.round((yearlyListPrice(plan) - plan.price_yearly) / plan.price);
}

function discountPercent(plan) {
    if (!plan.price_yearly) return 0;
    return Math.round((1 - plan.price_yearly / yearlyListPrice(plan)) * 100);
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
                                {{ formatPrice(periodFor(plan) === "yearly" ? plan.price_yearly : plan.price, periodFor(plan)) }}
                            </p>
                            <span
                                v-if="periodFor(plan) === 'yearly' && discountPercent(plan) > 0"
                                class="text-sm text-gray-400 line-through"
                            >
                                {{ formatPrice(yearlyListPrice(plan), "yearly") }}
                            </span>
                        </div>
                        <span
                            v-if="periodFor(plan) === 'yearly' && discountPercent(plan) > 0"
                            class="inline-block mt-1 text-[11px] font-medium text-brand-700 bg-brand-50 px-2 py-0.5 rounded-full"
                        >
                            -{{ discountPercent(plan) }}% &middot; Hemat {{ monthsSaved(plan) }} bulan
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
