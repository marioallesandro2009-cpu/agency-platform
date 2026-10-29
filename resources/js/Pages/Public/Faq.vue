<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import SpacePageHeader from "@/Components/Public/SpacePageHeader.vue";
import { Link } from "@inertiajs/vue3";
import { ref, computed } from "vue";

const props = defineProps({
    faqs: Array,
    categories: Array,
});

const activeCategory = ref("Semua");
const openFaq = ref(null);
const search = ref("");

const filteredFaqs = computed(() => {
    const q = search.value.trim().toLowerCase();
    return props.faqs.filter((faq) => {
        if (activeCategory.value !== "Semua" && faq.category !== activeCategory.value) return false;
        if (!q) return true;
        return faq.question.toLowerCase().includes(q) || faq.answer.toLowerCase().includes(q);
    });
});

function toggleFaq(id) {
    openFaq.value = openFaq.value === id ? null : id;
}

const pad = (n) => String(n).padStart(2, "0");

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
    <PublicLayout theme="space">
        <SpacePageHeader
            eyebrow="FAQ"
            code="???"
            title="Pertanyaan yang Sering Diajukan"
            subtitle="Temukan jawaban atas pertanyaan umum seputar layanan kami."
        />

        <section class="max-w-[1440px] mx-auto px-4 md:px-8 py-16 md:py-24 grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16">
            <!-- Left rail: search + category filter, sticks on desktop -->
            <aside class="lg:col-span-4">
                <div class="lg:sticky lg:top-28 space-y-8">
                    <div>
                        <label for="faq-search" class="block font-mono text-[10px] uppercase tracking-[0.25em] text-white/45 mb-3">Cari pertanyaan</label>
                        <div class="relative">
                            <input
                                id="faq-search"
                                v-model="search"
                                type="search"
                                placeholder="mis. domain, revisi, DP..."
                                class="w-full rounded-full border border-white/15 bg-white/[0.04] pl-5 pr-12 py-3 text-sm text-white placeholder:text-white/35 focus:outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-400/30"
                            />
                            <span class="absolute right-5 top-1/2 -translate-y-1/2 font-mono text-[10px] text-white/35 tabular-nums" aria-live="polite">{{ pad(filteredFaqs.length) }}</span>
                        </div>
                    </div>

                    <div v-if="categories.length">
                        <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-white/45 mb-3">Kategori</p>
                        <div class="flex flex-wrap lg:flex-col lg:items-start gap-2">
                            <button
                                type="button"
                                class="space-tab px-4 py-2 rounded-full font-mono text-[11px] uppercase tracking-[0.15em]"
                                :aria-pressed="activeCategory === 'Semua'"
                                @click="activeCategory = 'Semua'"
                            >
                                Semua
                            </button>
                            <button
                                v-for="category in categories"
                                :key="category"
                                type="button"
                                class="space-tab px-4 py-2 rounded-full font-mono text-[11px] uppercase tracking-[0.15em]"
                                :aria-pressed="activeCategory === category"
                                @click="activeCategory = category"
                            >
                                {{ category }}
                            </button>
                        </div>
                    </div>

                    <div class="hidden lg:block relative overflow-hidden rounded-2xl border border-brand-400/30 bg-space-deep p-6">
                        <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
                        <p class="relative font-display font-semibold text-lg text-white mb-2">Masih ada pertanyaan?</p>
                        <p class="relative text-sm text-white/55 mb-5">Tim kami siap membantu kebutuhan spesifik Anda.</p>
                        <Link href="/konsultasi" class="relative btn-space btn-space--pill">Konsultasi Gratis <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></Link>
                    </div>
                </div>
            </aside>

            <div class="lg:col-span-8">
                <div v-if="filteredFaqs.length === 0" class="rounded-2xl border border-dashed border-white/15 text-center py-20 px-6">
                    <p class="font-mono text-[11px] uppercase tracking-[0.2em] text-white/45">Tidak ada pertanyaan yang cocok.</p>
                </div>

                <div v-else class="border-t border-white/10">
                    <div
                        v-for="(faq, index) in filteredFaqs"
                        :key="faq.id"
                        v-scroll-reveal="{ delay: Math.min(index, 6) * 60 }"
                        class="border-b border-white/10 transition-colors"
                        :class="openFaq === faq.id ? 'bg-white/[0.02]' : ''"
                    >
                        <button
                            @click="toggleFaq(faq.id)"
                            :aria-expanded="openFaq === faq.id"
                            :aria-controls="'faq-answer-' + faq.id"
                            class="group w-full flex items-center gap-5 py-6 px-1 md:px-3 text-left"
                        >
                            <span class="font-mono text-[10px] tracking-[0.2em] shrink-0" :class="openFaq === faq.id ? 'text-brand-400' : 'text-white/35'">{{ pad(index + 1) }}</span>
                            <span class="flex-1">
                                <span v-if="faq.category && activeCategory === 'Semua'" class="block font-mono text-[9px] uppercase tracking-[0.2em] text-white/35 mb-1.5">{{ faq.category }}</span>
                                <span class="block font-semibold md:text-lg transition-colors group-hover:text-brand-300" :class="openFaq === faq.id ? 'text-white' : 'text-white/85'">{{ faq.question }}</span>
                            </span>
                            <span
                                aria-hidden="true"
                                class="w-9 h-9 shrink-0 rounded-full border flex items-center justify-center transition-all duration-300"
                                :class="openFaq === faq.id ? 'rotate-45 bg-brand-400 border-brand-400 text-space-void' : 'border-white/15 text-brand-400'"
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
                            <div v-if="openFaq === faq.id" :id="'faq-answer-' + faq.id" class="pb-7 pl-11 md:pl-14 pr-14 text-white/60 leading-relaxed">
                                {{ faq.answer }}
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>
        </section>

        <section class="lg:hidden max-w-[1440px] mx-auto px-4 md:px-8 pb-24">
            <div v-scroll-reveal class="relative overflow-hidden rounded-3xl border border-brand-400/30 bg-space-deep px-6 py-12 text-center">
                <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
                <h2 class="relative font-display font-semibold text-2xl text-white tracking-tight mb-3">Masih ada pertanyaan?</h2>
                <p class="relative text-white/55 mb-8">Tim kami siap membantu menjawab kebutuhan spesifik Anda.</p>
                <Link href="/konsultasi" class="relative btn-space btn-space--lg btn-space--pill">Konsultasi Gratis <span class="btn-space__arrow" aria-hidden="true">&rarr;</span></Link>
            </div>
        </section>
    </PublicLayout>
</template>
