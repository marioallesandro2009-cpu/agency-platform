<script setup>
/**
 * "Paket Maintenance Lanjutan" — sits below the tier-card renewal note on
 * /harga, category-independent (same 3 plans regardless of which pricing
 * tab is active). Deliberately lighter-weight cards than PricingCard so
 * this stays visually secondary to the Seed/Growth/Scale grid above it.
 */
import { computed } from "vue";
import { CircleCheck } from "lucide-vue-next";
import { usePage } from "@inertiajs/vue3";

defineProps({
    plans: { type: Array, default: () => [] },
});

const page = usePage();
const waNumber = computed(() => page.props.settings?.contact?.whatsapp_number);

function formatPrice(price) {
    return "Rp " + new Intl.NumberFormat("id-ID").format(price) + "/bulan";
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
                    <p class="text-lg font-bold text-gray-900 mb-4">{{ formatPrice(plan.price) }}</p>

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
