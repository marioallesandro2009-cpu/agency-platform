<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import AboutSection from "@/Components/Public/AboutSection.vue";
import Button from "@/Components/Public/Button.vue";
import ContactCtaForm from "@/Components/ContactCtaForm.vue";
import ServiceCard from "@/Components/Public/ServiceCard.vue";
import WhyUsCard from "@/Components/Public/WhyUsCard.vue";
import PricingCategoryGrid from "@/Components/Public/PricingCategoryGrid.vue";
import LiveTicker from "@/Components/Public/LiveTicker.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed, defineAsyncComponent, ref } from "vue";
import { MessageCircle, PenTool, Code, Rocket, Star } from "lucide-vue-next";

const PROCESS_ICONS = [MessageCircle, PenTool, Code, Rocket];

// Pulls gsap (the 3D-tilt/hover animation library) into its own lazily
// fetched chunk instead of bundling it into the homepage's critical initial
// JS — the "Portfolio" section it belongs to is an optional, admin-toggled,
// below-the-fold section that a given homepage config may not even render.
const AccordionGallery = defineAsyncComponent(() => import("@/Components/Public/AccordionGallery.vue"));

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
                <div class="max-w-7xl mx-auto px-4 py-24 sm:py-32 lg:py-36 relative z-10">
                    <div class="grid grid-cols-1 gap-14 lg:gap-20" :class="hero.image ? 'lg:grid-cols-2 lg:items-center' : ''">
                        <div :class="hero.image ? '' : 'text-center max-w-3xl mx-auto'">
                            <p v-scroll-reveal class="text-brand-400 font-semibold text-xs uppercase tracking-widest mb-4">{{ hero.eyebrow }}</p>
                            <h1
                                v-scroll-reveal="{ delay: 80 }"
                                class="text-5xl sm:text-6xl lg:text-7xl font-extrabold leading-[0.92] tracking-tight mb-6 [text-wrap:balance]"
                            >
                                {{ hero.title }}
                            </h1>
                            <p
                                v-scroll-reveal="{ delay: 160 }"
                                class="text-lg md:text-xl text-gray-300 leading-relaxed mb-10"
                                :class="hero.image ? 'max-w-lg' : 'max-w-2xl mx-auto'"
                            >
                                {{ hero.subtitle }}
                            </p>
                            <div v-scroll-reveal="{ delay: 240 }" class="flex flex-col sm:flex-row gap-4" :class="hero.image ? '' : 'justify-center'">
                                <Button :href="hero.cta_primary_url" size="large">{{ hero.cta_primary_text }}</Button>
                                <Button :href="hero.cta_secondary_url" variant="outline" size="large">{{ hero.cta_secondary_text }}</Button>
                            </div>
                        </div>

                        <div v-if="hero.image" v-scroll-reveal="{ direction: 'right', delay: 120 }" class="relative">
                            <div class="rounded-lg overflow-hidden shadow-elevated aspect-[4/3]">
                                <img :src="hero.image" :alt="hero.title" width="1200" height="900" fetchpriority="high" class="w-full h-full object-cover" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- About -->
            <AboutSection v-else-if="type === 'about' && about" :about="about" />

            <!-- Services -->
            <section v-else-if="type === 'services'" class="max-w-7xl mx-auto px-4 py-20 md:py-24">
                <div v-scroll-reveal class="text-center max-w-2xl mx-auto mb-12">
                    <p class="text-brand-600 font-semibold text-xs uppercase tracking-widest mb-2">{{ servicesSection?.subtitle || "Layanan Kami" }}</p>
                    <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-gray-900">{{ servicesSection?.title || "Solusi Website untuk Setiap Kebutuhan" }}</h2>
                </div>
                <div class="max-w-5xl mx-auto">
                    <div v-for="(service, index) in services" :key="service.id" v-scroll-reveal="{ delay: index * 60 }">
                        <ServiceCard :service="service" :index="index" />
                    </div>
                </div>
            </section>

            <!-- Portfolio -->
            <section v-else-if="type === 'portfolio' && portfolioProjects?.length" class="max-w-6xl mx-auto px-4 py-20 md:py-24">
                <div v-scroll-reveal class="text-center max-w-2xl mx-auto mb-12">
                    <p class="text-brand-600 font-semibold text-xs uppercase tracking-widest mb-2">{{ portfolioSection?.subtitle || "Portfolio" }}</p>
                    <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-gray-900">{{ portfolioSection?.title || "Hasil Karya Kami" }}</h2>
                </div>
                <div v-scroll-reveal="{ delay: 100 }">
                    <AccordionGallery :items="portfolioGalleryItems" />
                </div>
                <div class="text-center mt-12">
                    <Link href="/portfolio" class="text-brand-600 font-semibold text-sm hover:underline">Lihat semua portfolio -&gt;</Link>
                </div>
            </section>

            <!-- Why Us -->
            <section v-else-if="type === 'why_us' && whyUs" class="bg-gray-50 py-20 md:py-24">
                <div class="max-w-7xl mx-auto px-4">
                    <div v-scroll-reveal class="text-center max-w-2xl mx-auto mb-12">
                        <p class="text-brand-600 font-semibold text-xs uppercase tracking-widest mb-2">{{ whyUs.subtitle }}</p>
                        <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-gray-900">{{ whyUs.title }}</h2>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                        <div v-for="(item, index) in whyUs.items" :key="item.title" v-scroll-reveal="{ delay: index * 80 }">
                            <WhyUsCard :title="item.title" :description="item.description" :icon="item.icon" :index="index" />
                        </div>
                    </div>
                </div>
            </section>

            <!-- Process -->
            <section v-else-if="type === 'process' && process" class="max-w-7xl mx-auto px-4 py-20 md:py-24">
                <div v-scroll-reveal class="text-center max-w-2xl mx-auto mb-12">
                    <p class="text-brand-600 font-semibold text-xs uppercase tracking-widest mb-2">{{ process.subtitle }}</p>
                    <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-gray-900">{{ process.title }}</h2>
                </div>
                <div class="relative">
                    <div class="hidden md:block absolute top-14 left-[12.5%] right-[12.5%] h-0.5 bg-brand-200"></div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div
                            v-for="(step, index) in process.steps"
                            :key="step.title"
                            v-scroll-reveal="{ delay: index * 80 }"
                            class="relative text-center px-4"
                        >
                            <div class="process-icon-3d relative w-16 h-16 mx-auto mb-4" :style="{ animationDelay: index * 200 + 'ms' }">
                                <div class="w-16 h-16 rounded-full bg-brand-600 text-white flex items-center justify-center">
                                    <component :is="PROCESS_ICONS[index]" class="w-7 h-7" aria-hidden="true" />
                                </div>
                                <span class="absolute -top-1 -right-1 w-6 h-6 rounded-full bg-white border-2 border-brand-600 text-brand-600 text-xs font-bold flex items-center justify-center">{{ index + 1 }}</span>
                            </div>
                            <h3 class="font-bold tracking-tight text-gray-900 mb-2">{{ step.title }}</h3>
                            <p class="text-sm text-gray-500 leading-relaxed">{{ step.description }}</p>
                        </div>
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
            <section v-else-if="type === 'testimonials'" class="max-w-7xl mx-auto px-4 py-20 md:py-24">
                <div v-scroll-reveal class="text-center max-w-2xl mx-auto mb-14">
                    <p class="text-brand-600 font-semibold text-xs uppercase tracking-widest mb-2">{{ testimonialsSection?.subtitle || "Testimoni" }}</p>
                    <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-gray-900">{{ testimonialsSection?.title || "Apa Kata Klien Kami" }}</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-10 md:gap-12">
                    <div v-for="(t, index) in testimonials" :key="t.id" v-scroll-reveal="{ delay: index * 80 }">
                        <div class="flex gap-0.5 mb-4" aria-hidden="true">
                            <Star v-for="n in 5" :key="n" class="w-4 h-4" :class="n <= t.rating ? 'fill-brand-500 text-brand-500' : 'text-gray-200'" />
                        </div>
                        <p class="text-gray-700 leading-relaxed mb-5">&ldquo;{{ t.testimonial }}&rdquo;</p>
                        <p class="font-bold tracking-tight text-gray-900 text-sm">{{ t.name }}</p>
                        <p class="text-xs text-gray-500">{{ t.position }}<span v-if="t.company">, {{ t.company }}</span></p>
                    </div>
                </div>
            </section>

            <!-- FAQ -->
            <section v-else-if="type === 'faq'" class="bg-gray-50 py-20">
                <div class="max-w-3xl mx-auto px-4">
                    <div v-scroll-reveal class="text-center mb-14">
                        <p class="text-brand-600 font-semibold text-xs uppercase tracking-widest mb-2">{{ faqSection?.subtitle || "FAQ" }}</p>
                        <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-gray-900">{{ faqSection?.title || "Pertanyaan yang Sering Diajukan" }}</h2>
                    </div>
                    <div class="border-t border-gray-200">
                        <div
                            v-for="(faq, index) in faqs"
                            :key="faq.id"
                            v-scroll-reveal="{ delay: index * 80 }"
                            class="border-b border-gray-200"
                        >
                            <button
                                @click="toggleFaq(faq.id)"
                                :aria-expanded="openFaq === faq.id"
                                :aria-controls="'home-faq-answer-' + faq.id"
                                class="group w-full flex items-center justify-between py-5 text-left"
                            >
                                <span class="font-semibold text-gray-900 transition-colors group-hover:text-brand-600">{{ faq.question }}</span>
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
                                <div v-if="openFaq === faq.id" :id="'home-faq-answer-' + faq.id" class="pb-5 text-gray-600 leading-relaxed">{{ faq.answer }}</div>
                            </Transition>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Blog -->
            <section v-else-if="type === 'blog' && latestPosts?.length" class="max-w-7xl mx-auto px-4 py-20 md:py-24">
                <div v-scroll-reveal class="text-center max-w-2xl mx-auto mb-14">
                    <p class="text-brand-600 font-semibold text-xs uppercase tracking-widest mb-2">{{ blogSection?.subtitle || "Blog" }}</p>
                    <h2 class="text-3xl md:text-4xl font-bold tracking-tight text-gray-900">{{ blogSection?.title || "Artikel & Insight Terbaru" }}</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <article v-for="(post, index) in latestPosts" :key="post.id" v-scroll-reveal="{ delay: index * 80 }" class="group">
                        <Link :href="'/blog/' + post.slug" class="block">
                            <div class="aspect-[5/3] rounded-lg overflow-hidden bg-gray-100 mb-4">
                                <img
                                    v-if="post.featured_image"
                                    :src="post.featured_image"
                                    :alt="post.title"
                                    width="400"
                                    height="240"
                                    loading="lazy"
                                    class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105"
                                />
                            </div>
                            <p v-if="post.category" class="text-xs text-brand-600 font-semibold uppercase tracking-wide mb-2">{{ post.category.name }}</p>
                            <h3 class="font-bold tracking-tight text-gray-900 mb-2 line-clamp-2 transition-colors group-hover:text-brand-600">{{ post.title }}</h3>
                            <p class="text-xs text-gray-500">{{ formatDate(post.published_at) }}</p>
                        </Link>
                    </article>
                </div>
                <div class="text-center mt-12">
                    <Link href="/blog" class="text-brand-600 font-semibold text-sm hover:underline">Lihat semua artikel -&gt;</Link>
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

<style scoped>
/* Ambient (non-hover) 3D wobble for the "Proses Kerja Kami" step icons —
   runs continuously on a perspective tilt, independent of :hover. */
.process-icon-3d {
    perspective: 500px;
}
.process-icon-3d > div {
    animation: process-icon-tilt 5s ease-in-out infinite;
    transform-style: preserve-3d;
}
@keyframes process-icon-tilt {
    0%, 100% {
        transform: rotateY(-14deg) rotateX(5deg);
    }
    50% {
        transform: rotateY(14deg) rotateX(-5deg);
    }
}
@media (prefers-reduced-motion: reduce) {
    .process-icon-3d > div {
        animation: none;
    }
}
</style>