<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import PageHeaderScanner from "@/Components/Public/PageHeaderScanner.vue";
import ContactCtaForm from "@/Components/ContactCtaForm.vue";
import Button from "@/Components/Public/Button.vue";
import LucideIcon from "@/Components/Public/LucideIcon.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { Check, MessageCircle } from "lucide-vue-next";

const props = defineProps({
    service: Object,
    otherServices: Array,
    relatedPosts: Array,
    relatedPortfolio: Array,
});

const page = usePage();
const mascots = page.props.mascots || {};
const waNumber = computed(() => page.props.settings?.contact?.whatsapp_number);

// Each /jasa-website-* page gets its own sidebar mascot slot instead of a
// shared one — keyed by the service's slug (matches the route defaults in
// routes/web.php and SitemapController's $serviceRoutes map).
const SIDEBAR_MASCOT_KEYS = {
    "jasa-website-umkm": "sidebar_jasa_umkm",
    "jasa-website-company-profile": "sidebar_jasa_company_profile",
    "jasa-toko-online": "sidebar_jasa_toko_online",
    "jasa-web-development": "sidebar_jasa_web_development",
};
const sidebarMascot = computed(() => mascots[SIDEBAR_MASCOT_KEYS[props.service.slug]] || {});

const jsonLd = computed(() => ({
    "@context": "https://schema.org",
    "@type": "Service",
    "name": props.service.name,
    "description": props.service.seo_description || props.service.short_description,
    "provider": {
        "@type": "Organization",
        "name": page.props.settings?.general?.website_name,
    },
}));

function formatDate(date) {
    if (!date) return "";
    return new Date(date).toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" });
}

const breadcrumbItems = computed(() => [
    { label: "Home", href: "/" },
    { label: "Jasa & Layanan", href: "/jasa-website" },
    { label: props.service.name },
]);

const openFaq = ref(null);
function toggleFaq(index) {
    openFaq.value = openFaq.value === index ? null : index;
}
</script>

<template>
    <SeoHead
        :title="service.seo_title || service.name"
        :description="service.seo_description || service.short_description"
        :json-ld="jsonLd"
    />
    <PublicLayout>
        <PageHeaderScanner
            eyebrow="Jasa & Layanan"
            :title="service.name"
            :subtitle="service.short_description"
            :breadcrumb-items="breadcrumbItems"
        />
        <div class="max-w-7xl mx-auto px-4 py-16 md:py-20 grid grid-cols-1 lg:grid-cols-3 gap-10 lg:gap-16">
            <!-- Main content -->
            <div v-scroll-reveal class="lg:col-span-2">
                <div class="prose text-lg text-gray-700 leading-relaxed whitespace-pre-line" v-html="service.long_description || service.description"></div>

                <div v-if="service.target_audience" class="mt-10 bg-brand-50 border border-brand-100 rounded-xl p-6">
                    <h2 class="text-sm font-bold uppercase tracking-widest text-brand-700 mb-2">Untuk Siapa Layanan Ini?</h2>
                    <p class="text-gray-700 leading-relaxed">{{ service.target_audience }}</p>
                </div>

                <div v-if="service.features?.length" class="mt-12">
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 mb-5">Fitur</h2>
                    <ul class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-base text-gray-600">
                        <li v-for="f in service.features" :key="f" class="flex items-start gap-2.5">
                            <Check class="w-5 h-5 text-brand-600 shrink-0 mt-0.5" aria-hidden="true" />
                            <span>{{ f }}</span>
                        </li>
                    </ul>
                </div>

                <div v-if="service.benefits?.length" class="mt-12">
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 mb-5">Manfaat</h2>
                    <ul class="space-y-3 text-base text-gray-600">
                        <li v-for="b in service.benefits" :key="b" class="flex items-start gap-2.5">
                            <Check class="w-5 h-5 text-brand-600 shrink-0 mt-0.5" aria-hidden="true" />
                            <span>{{ b }}</span>
                        </li>
                    </ul>
                </div>

                <div v-if="service.process_steps?.length" class="mt-12">
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 mb-6">Proses Pengerjaan</h2>
                    <div class="relative">
                        <div class="hidden sm:block absolute top-5 left-[10%] right-[10%] h-0.5 bg-brand-200" aria-hidden="true"></div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <div v-for="(step, index) in service.process_steps" :key="step" class="relative text-center sm:text-left">
                                <div class="relative z-10 w-10 h-10 rounded-full bg-brand-600 text-white flex items-center justify-center font-bold text-sm mx-auto sm:mx-0 mb-3">
                                    {{ index + 1 }}
                                </div>
                                <p class="text-sm font-medium text-gray-800">{{ step }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-12 pt-8 border-t border-gray-200">
                    <div class="flex flex-wrap items-center gap-6 sm:gap-8">
                        <Button :href="service.cta_url || '/konsultasi'">{{ service.cta_text || "Konsultasi Gratis Sekarang" }}</Button>

                        <div v-if="sidebarMascot.image_path" class="relative shrink-0 w-28 h-28 sm:w-36 sm:h-36 lg:w-48 lg:h-48 ambient-float">
                            <div class="absolute inset-2 -z-10 rounded-full bg-brand-50" aria-hidden="true"></div>
                            <img
                                :src="sidebarMascot.image_path"
                                :alt="sidebarMascot.alt_text || ''"
                                width="220"
                                height="220"
                                loading="lazy"
                                class="w-full h-full object-contain"
                            />
                        </div>
                    </div>
                    <p v-if="sidebarMascot.caption" class="text-xs text-gray-500 mt-3">{{ sidebarMascot.caption }}</p>
                </div>

                <div v-if="relatedPortfolio?.length" class="mt-12 pt-8 border-t border-gray-200">
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 mb-5">Portfolio Terkait</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        <Link
                            v-for="project in relatedPortfolio"
                            :key="project.id"
                            :href="'/portfolio/' + project.slug"
                            class="group block rounded-xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow"
                        >
                            <div class="aspect-[4/3] bg-gray-100 overflow-hidden">
                                <img
                                    v-if="project.thumbnail"
                                    :src="project.thumbnail"
                                    :alt="project.name"
                                    width="320"
                                    height="240"
                                    loading="lazy"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />
                            </div>
                            <div class="p-4">
                                <p class="font-semibold text-gray-900 group-hover:text-brand-600 transition-colors line-clamp-1">{{ project.name }}</p>
                                <p v-if="project.short_description" class="text-sm text-gray-500 mt-1 line-clamp-2">{{ project.short_description }}</p>
                            </div>
                        </Link>
                    </div>
                </div>

                <div v-if="service.faqs?.length" class="mt-12 pt-8 border-t border-gray-200">
                    <h2 class="text-2xl font-bold tracking-tight text-gray-900 mb-5">Pertanyaan Umum</h2>
                    <div class="border-t border-gray-200">
                        <div v-for="(faq, index) in service.faqs" :key="index" class="border-b border-gray-200">
                            <button
                                @click="toggleFaq(index)"
                                :aria-expanded="openFaq === index"
                                :aria-controls="'service-faq-answer-' + index"
                                class="group w-full flex items-center justify-between py-5 text-left"
                            >
                                <span class="font-semibold text-gray-900 transition-colors group-hover:text-brand-600">{{ faq.question }}</span>
                                <span
                                    aria-hidden="true"
                                    class="text-brand-600 text-lg flex-shrink-0 ml-3 inline-block transition-transform duration-300"
                                    :class="openFaq === index ? 'rotate-45' : ''"
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
                                <div v-if="openFaq === index" :id="'service-faq-answer-' + index" class="pb-5 text-gray-600 leading-relaxed">{{ faq.answer }}</div>
                            </Transition>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <aside v-scroll-reveal="{ direction: 'right' }" class="space-y-8">
                <!-- Related articles -->
                <div v-if="relatedPosts?.length" class="[&:not(:first-child)]:pt-8 [&:not(:first-child)]:border-t [&:not(:first-child)]:border-gray-100">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-5">Terkait</h3>
                    <div class="space-y-4">
                        <Link
                            v-for="post in relatedPosts"
                            :key="post.id"
                            :href="'/blog/' + post.slug"
                            class="block group"
                        >
                            <p class="text-sm font-medium text-gray-800 group-hover:text-brand-600 line-clamp-2">{{ post.title }}</p>
                            <p class="text-xs text-gray-500 mt-1">{{ formatDate(post.published_at) }}</p>
                        </Link>
                    </div>
                </div>

                <!-- Other services -->
                <div v-if="otherServices?.length" class="[&:not(:first-child)]:pt-8 [&:not(:first-child)]:border-t [&:not(:first-child)]:border-gray-100">
                    <h3 class="text-xs font-bold uppercase tracking-widest text-gray-500 mb-5">Layanan Lainnya</h3>
                    <div class="space-y-3">
                        <Link
                            v-for="s in otherServices"
                            :key="s.id"
                            :href="'/' + s.slug"
                            class="group flex items-center gap-3 rounded-lg border border-gray-100 shadow-sm p-3 hover:shadow-md hover:border-brand-100 transition-all"
                        >
                            <div class="w-11 h-11 rounded-md bg-brand-50 flex items-center justify-center shrink-0 overflow-hidden">
                                <img v-if="s.image" :src="s.image" :alt="''" aria-hidden="true" width="44" height="44" loading="lazy" class="w-full h-full object-cover" />
                                <LucideIcon v-else :name="s.icon" class="w-5 h-5 text-brand-600" />
                            </div>
                            <span class="text-sm font-medium text-gray-800 group-hover:text-brand-600 transition-colors flex-1 min-w-0 line-clamp-2">{{ s.name }}</span>
                            <span aria-hidden="true" class="text-brand-600 opacity-0 -translate-x-1 transition-all duration-200 group-hover:opacity-100 group-hover:translate-x-0">&rarr;</span>
                        </Link>
                    </div>
                </div>

                <!-- Consultation CTA card -->
                <div class="[&:not(:first-child)]:pt-8 [&:not(:first-child)]:border-t [&:not(:first-child)]:border-gray-100">
                    <div class="rounded-xl bg-charcoal text-white p-6">
                        <h3 class="font-bold text-lg mb-2">Butuh Bantuan Menentukan Paket?</h3>
                        <p class="text-sm text-gray-300 mb-5 leading-relaxed">Konsultasikan kebutuhan Anda langsung, gratis tanpa komitmen.</p>
                        <div class="flex flex-col gap-2.5">
                            <Button
                                v-if="waNumber"
                                :href="'https://wa.me/' + waNumber + '?text=' + encodeURIComponent('Halo, saya mau konsultasi soal ' + service.name)"
                                external
                                class="w-full"
                            >
                                <MessageCircle class="w-4 h-4" aria-hidden="true" />
                                Chat WhatsApp
                            </Button>
                            <Button :href="service.cta_url || '/konsultasi'" variant="outline" class="w-full">
                                {{ service.cta_text || "Konsultasi Gratis" }}
                            </Button>
                        </div>
                    </div>
                </div>
            </aside>
        </div>

        <ContactCtaForm
            eyebrow="Mulai Sekarang"
            :title="'Siap Membangun ' + service.name + ' Anda?'"
            :description="'Ceritakan kebutuhan Anda, tim kami akan bantu wujudkan dalam waktu singkat.'"
        />
    </PublicLayout>
</template>
