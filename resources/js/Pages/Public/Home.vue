<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import AccordionGallery from "@/Components/Public/AccordionGallery.vue";
import AboutSection from "@/Components/Public/AboutSection.vue";
import ContactCtaForm from "@/Components/ContactCtaForm.vue";
import ServiceCard from "@/Components/Public/ServiceCard.vue";
import WhyUsCard from "@/Components/Public/WhyUsCard.vue";
import PricingCategoryGrid from "@/Components/Public/PricingCategoryGrid.vue";
import LiveTicker from "@/Components/Public/LiveTicker.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const page = usePage();

const props = defineProps({
    sections: Array,
    hero: Object,
    about: Object,
    servicesSection: Object,
    services: Array,
    whyUs: Object,
    process: Object,
    pricingSection: Object,
    pricingPackages: Array,
    testimonialsSection: Object,
    testimonials: Array,
    faqSection: Object,
    faqs: Array,
    ctaSection: Object,
    portfolioSection: Object,
    portfolioProjects: Array,
    blogSection: Object,
    latestPosts: Array,
    recentActivity: Array,
});

function formatDate(date) {
    if (!date) return "";
    return new Date(date).toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" });
}

const portfolioGalleryItems = computed(() =>
    (props.portfolioProjects || []).map((project) => ({
        image: project.thumbnail,
        label: project.name,
        link: "/portfolio/" + project.slug,
        badge: project.project_type === "personal" ? "Personal Project" : null,
    }))
);

const openFaq = ref(null);
function toggleFaq(id) {
    openFaq.value = openFaq.value === id ? null : id;
}
</script>

<template>
    <SeoHead :title="page.props.settings?.general?.tagline" />
    <PublicLayout>
        <LiveTicker :activities="recentActivity" />
        <template v-for="type in sections" :key="type">
            <!-- Hero -->
            <section v-if="type === 'hero' && hero" class="relative bg-charcoal text-white overflow-hidden">
                <template v-if="hero.image">
                    <img :src="hero.image" alt="" class="absolute inset-0 w-full h-full object-cover opacity-40" />
                    <div class="absolute inset-0 bg-charcoal/80"></div>
                </template>
                <div class="max-w-4xl mx-auto px-4 py-24 text-center relative z-10">
                    <p v-scroll-reveal class="text-brand-400 font-semibold text-sm uppercase tracking-wide mb-3">{{ hero.eyebrow }}</p>
                    <h1 v-scroll-reveal="{ delay: 80 }" class="text-4xl md:text-5xl font-bold mb-6 leading-tight">{{ hero.title }}</h1>
                    <p v-scroll-reveal="{ delay: 160 }" class="text-gray-300 text-lg mb-8 max-w-2xl mx-auto">{{ hero.subtitle }}</p>
                    <div v-scroll-reveal="{ delay: 240 }" class="flex flex-col sm:flex-row gap-4 justify-center">
                        <Link :href="hero.cta_primary_url" class="bg-brand-600 hover:bg-brand-700 text-white font-medium px-6 py-3 rounded-md">
                            {{ hero.cta_primary_text }}
                        </Link>
                        <Link :href="hero.cta_secondary_url" class="border border-gray-500 hover:border-white text-white font-medium px-6 py-3 rounded-md">
                            {{ hero.cta_secondary_text }}
                        </Link>
                    </div>
                </div>
            </section>

            <!-- About -->
            <AboutSection v-else-if="type === 'about' && about" :about="about" />

            <!-- Services -->
            <section v-else-if="type === 'services'" class="max-w-7xl mx-auto px-4 py-20">
                <div v-scroll-reveal class="text-center max-w-2xl mx-auto mb-12">
                    <p class="text-brand-600 font-semibold text-sm uppercase tracking-wide mb-2">{{ servicesSection?.subtitle || "Layanan Kami" }}</p>
                    <h2 class="text-3xl font-bold text-gray-900">{{ servicesSection?.title || "Solusi Website untuk Setiap Kebutuhan" }}</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div v-for="(service, index) in services" :key="service.id" v-scroll-reveal="{ delay: index * 80 }">
                        <ServiceCard :service="service" :index="index" />
                    </div>
                </div>
            </section>

            <!-- Portfolio -->
            <section v-else-if="type === 'portfolio' && portfolioProjects?.length" class="max-w-6xl mx-auto px-4 py-20">
                <div v-scroll-reveal class="text-center max-w-2xl mx-auto mb-12">
                    <p class="text-brand-600 font-semibold text-sm uppercase tracking-wide mb-2">{{ portfolioSection?.subtitle || "Portfolio" }}</p>
                    <h2 class="text-3xl font-bold text-gray-900">{{ portfolioSection?.title || "Hasil Karya Kami" }}</h2>
                </div>
                <div v-scroll-reveal="{ delay: 100 }">
                    <AccordionGallery :items="portfolioGalleryItems" />
                </div>
                <div class="text-center mt-8">
                    <Link href="/portfolio" class="text-brand-600 font-medium hover:underline">Lihat semua portfolio -&gt;</Link>
                </div>
            </section>

            <!-- Why Us -->
            <section v-else-if="type === 'why_us' && whyUs" class="bg-gray-50 py-20">
                <div class="max-w-7xl mx-auto px-4">
                    <div v-scroll-reveal class="text-center max-w-2xl mx-auto mb-12">
                        <p class="text-brand-600 font-semibold text-sm uppercase tracking-wide mb-2">{{ whyUs.subtitle }}</p>
                        <h2 class="text-3xl font-bold text-gray-900">{{ whyUs.title }}</h2>
                    </div>
                    <template v-if="whyUs.items.length > 2">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-2xl mx-auto mb-8">
                            <div v-for="(item, index) in whyUs.items.slice(0, 2)" :key="item.title" v-scroll-reveal="{ delay: index * 80 }">
                                <WhyUsCard :title="item.title" :description="item.description" :icon="item.icon" :index="index" />
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                            <div v-for="(item, index) in whyUs.items.slice(2)" :key="item.title" v-scroll-reveal="{ delay: (index + 2) * 80 }">
                                <WhyUsCard :title="item.title" :description="item.description" :icon="item.icon" :index="index + 2" />
                            </div>
                        </div>
                    </template>
                    <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-2xl mx-auto">
                        <div v-for="(item, index) in whyUs.items" :key="item.title" v-scroll-reveal="{ delay: index * 80 }">
                            <WhyUsCard :title="item.title" :description="item.description" :icon="item.icon" :index="index" />
                        </div>
                    </div>
                </div>
            </section>

            <!-- Process -->
            <section v-else-if="type === 'process' && process" class="max-w-7xl mx-auto px-4 py-20">
                <div v-scroll-reveal class="text-center max-w-2xl mx-auto mb-12">
                    <p class="text-brand-600 font-semibold text-sm uppercase tracking-wide mb-2">{{ process.subtitle }}</p>
                    <h2 class="text-3xl font-bold text-gray-900">{{ process.title }}</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div
                        v-for="(step, index) in process.steps"
                        :key="step.title"
                        v-scroll-reveal="{ delay: index * 80 }"
                        class="text-center"
                    >
                        <div class="w-10 h-10 rounded-full bg-brand-600 text-white flex items-center justify-center font-semibold mx-auto mb-3">{{ index + 1 }}</div>
                        <h3 class="font-semibold text-gray-900 mb-2">{{ step.title }}</h3>
                        <p class="text-sm text-gray-500">{{ step.description }}</p>
                    </div>
                </div>
            </section>

            <!-- Pricing -->
            <PricingCategoryGrid
                v-else-if="type === 'pricing'"
                :packages="pricingPackages"
                :title="pricingSection?.title || 'Paket Harga'"
                :subtitle="pricingSection?.subtitle || 'Investasi'"
            />

            <!-- Testimonials -->
            <section v-else-if="type === 'testimonials'" class="max-w-7xl mx-auto px-4 py-20">
                <div v-scroll-reveal class="text-center max-w-2xl mx-auto mb-12">
                    <p class="text-brand-600 font-semibold text-sm uppercase tracking-wide mb-2">{{ testimonialsSection?.subtitle || "Testimoni" }}</p>
                    <h2 class="text-3xl font-bold text-gray-900">{{ testimonialsSection?.title || "Apa Kata Klien Kami" }}</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        v-for="(t, index) in testimonials"
                        :key="t.id"
                        v-scroll-reveal="{ delay: index * 80 }"
                        class="bg-white border border-black/10 rounded-xl p-6 transition-all duration-300 hover:-translate-y-1 hover:border-brand-300 hover:shadow-lg"
                    >
                        <p class="text-yellow-500 text-sm mb-3">
                            <span v-for="n in t.rating" :key="n">*</span><span v-for="n in (5 - t.rating)" :key="'e' + n" class="text-gray-400">*</span>
                        </p>
                        <p class="text-sm text-gray-600 mb-4 italic">"{{ t.testimonial }}"</p>
                        <p class="font-semibold text-gray-900 text-sm">{{ t.name }}</p>
                        <p class="text-xs text-gray-500">{{ t.position }}<span v-if="t.company">, {{ t.company }}</span></p>
                    </div>
                </div>
            </section>

            <!-- FAQ -->
            <section v-else-if="type === 'faq'" class="bg-gray-50 py-20">
                <div class="max-w-3xl mx-auto px-4">
                    <div v-scroll-reveal class="text-center mb-12">
                        <p class="text-brand-600 font-semibold text-sm uppercase tracking-wide mb-2">{{ faqSection?.subtitle || "FAQ" }}</p>
                        <h2 class="text-3xl font-bold text-gray-900">{{ faqSection?.title || "Pertanyaan yang Sering Diajukan" }}</h2>
                    </div>
                    <div class="space-y-3">
                        <div
                            v-for="(faq, index) in faqs"
                            :key="faq.id"
                            v-scroll-reveal="{ delay: index * 80 }"
                            class="bg-white border border-black/10 rounded-lg overflow-hidden transition-colors duration-300 hover:border-brand-300"
                        >
                            <button
                                @click="toggleFaq(faq.id)"
                                :aria-expanded="openFaq === faq.id"
                                :aria-controls="'home-faq-answer-' + faq.id"
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
                                <div v-if="openFaq === faq.id" :id="'home-faq-answer-' + faq.id" class="px-5 pb-4 text-sm text-gray-600">{{ faq.answer }}</div>
                            </Transition>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Blog -->
            <section v-else-if="type === 'blog' && latestPosts?.length" class="max-w-7xl mx-auto px-4 py-20">
                <div v-scroll-reveal class="text-center max-w-2xl mx-auto mb-12">
                    <p class="text-brand-600 font-semibold text-sm uppercase tracking-wide mb-2">{{ blogSection?.subtitle || "Blog" }}</p>
                    <h2 class="text-3xl font-bold text-gray-900">{{ blogSection?.title || "Artikel & Insight Terbaru" }}</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <article
                        v-for="(post, index) in latestPosts"
                        :key="post.id"
                        v-scroll-reveal="{ delay: index * 80 }"
                        class="border border-black/10 rounded-xl overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:border-brand-300 hover:shadow-lg"
                    >
                        <div class="h-40 bg-gray-100">
                            <img v-if="post.featured_image" :src="post.featured_image" :alt="post.title" class="w-full h-full object-cover" />
                        </div>
                        <div class="p-5">
                            <p v-if="post.category" class="text-xs text-brand-600 font-medium mb-2">{{ post.category.name }}</p>
                            <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">
                                <Link :href="'/blog/' + post.slug" class="hover:text-brand-600">{{ post.title }}</Link>
                            </h3>
                            <p class="text-xs text-gray-500">{{ formatDate(post.published_at) }}</p>
                        </div>
                    </article>
                </div>
                <div class="text-center mt-8">
                    <Link href="/blog" class="text-brand-600 font-medium hover:underline">Lihat semua artikel -&gt;</Link>
                </div>
            </section>

            <!-- Final CTA -->
            <ContactCtaForm
                v-else-if="type === 'cta' && ctaSection"
                :eyebrow="ctaSection.subtitle"
                :title="ctaSection.title"
                :description="ctaSection.description"
            />
        </template>
    </PublicLayout>
</template>