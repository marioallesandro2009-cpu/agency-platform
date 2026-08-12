<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import PageHeaderScanner from "@/Components/Public/PageHeaderScanner.vue";
import { Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    faqs: Array,
    categories: Array,
});

const activeCategory = ref("Semua");
const openFaq = ref(null);

const filteredFaqs = computed(() => {
    if (activeCategory.value === "Semua") return props.faqs;
    return props.faqs.filter((faq) => faq.category === activeCategory.value);
});

function toggleFaq(id) {
    openFaq.value = openFaq.value === id ? null : id;
}

const jsonLd = computed(() => ({
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": props.faqs.map((faq) => ({
        "@type": "Question",
        "name": faq.question,
        "acceptedAnswer": { "@type": "Answer", "text": faq.answer },
    })),
}));
</script>

<template>
    <SeoHead
        title="FAQ"
        description="Pertanyaan yang sering diajukan seputar jasa pembuatan website kami."
        :json-ld="jsonLd"
    />
    <PublicLayout>
        <PageHeaderScanner
            eyebrow="FAQ"
            title="Pertanyaan yang Sering Diajukan"
            subtitle="Temukan jawaban atas pertanyaan umum seputar layanan kami."
        />

        <section class="max-w-3xl mx-auto px-4 py-16">
            <div v-if="categories.length" class="flex flex-wrap gap-2 justify-center mb-10">
                <button
                    @click="activeCategory = 'Semua'"
                    :class="[
                        'text-sm font-medium px-4 py-2 rounded-full border transition-colors',
                        activeCategory === 'Semua' ? 'bg-brand-600 text-white border-brand-600' : 'border-gray-300 text-gray-600 hover:bg-gray-50',
                    ]"
                >
                    Semua
                </button>
                <button
                    v-for="category in categories"
                    :key="category"
                    @click="activeCategory = category"
                    :class="[
                        'text-sm font-medium px-4 py-2 rounded-full border transition-colors',
                        activeCategory === category ? 'bg-brand-600 text-white border-brand-600' : 'border-gray-300 text-gray-600 hover:bg-gray-50',
                    ]"
                >
                    {{ category }}
                </button>
            </div>

            <div v-if="filteredFaqs.length === 0" class="text-center text-gray-500 py-12">
                Belum ada FAQ untuk kategori ini.
            </div>

            <div v-else class="space-y-3">
                <div
                    v-for="(faq, index) in filteredFaqs"
                    :key="faq.id"
                    v-scroll-reveal="{ delay: Math.min(index, 6) * 80 }"
                    class="bg-white border border-black/10 rounded-lg overflow-hidden transition-colors duration-300 hover:border-brand-300"
                >
                    <button
                        @click="toggleFaq(faq.id)"
                        :aria-expanded="openFaq === faq.id"
                        :aria-controls="'faq-answer-' + faq.id"
                        class="w-full flex items-center justify-between px-5 py-4 text-left"
                    >
                        <span class="font-medium text-gray-900 text-sm">{{ faq.question }}</span>
                        <span
                            aria-hidden="true"
                            class="text-brand-600 text-lg flex-shrink-0 ml-3 inline-block transition-transform duration-300"
                            :class="openFaq === faq.id ? 'rotate-45' : ''"
                        >+</span>
                    </button>
                    <Transition
                        enter-active-class="transition-all duration-300 ease-out"
                        enter-from-class="opacity-0 -translate-y-1"
                        enter-to-class="opacity-100 translate-y-0"
                        leave-active-class="transition-all duration-200 ease-in"
                        leave-from-class="opacity-100 translate-y-0"
                        leave-to-class="opacity-0 -translate-y-1"
                    >
                        <div v-if="openFaq === faq.id" :id="'faq-answer-' + faq.id" class="px-5 pb-4 text-sm text-gray-600">
                            {{ faq.answer }}
                        </div>
                    </Transition>
                </div>
            </div>
        </section>

        <section class="bg-gray-50 py-16">
            <div v-scroll-reveal class="max-w-xl mx-auto px-4 text-center">
                <h2 class="text-xl font-semibold text-gray-900 mb-2">Masih ada pertanyaan?</h2>
                <p class="text-gray-500 mb-6">Tim kami siap membantu menjawab kebutuhan spesifik Anda.</p>
                <Link href="/konsultasi" class="inline-block bg-brand-600 text-white font-medium px-6 py-3 rounded-md hover:bg-brand-700">
                    Konsultasi Gratis
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>