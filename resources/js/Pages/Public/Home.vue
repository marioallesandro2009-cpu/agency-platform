<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import AboutSection from "@/Components/Public/AboutSection.vue";
import Button from "@/Components/Public/Button.vue";
import SpaceSectionHeading from "@/Components/Public/SpaceSectionHeading.vue";
import ContactCtaForm from "@/Components/ContactCtaForm.vue";
import ServiceCard from "@/Components/Public/ServiceCard.vue";
import WhyUsCard from "@/Components/Public/WhyUsCard.vue";
import PricingCategoryGrid from "@/Components/Public/PricingCategoryGrid.vue";
import LiveTicker from "@/Components/Public/LiveTicker.vue";
import { Link, usePage } from "@inertiajs/vue3";
import { computed, defineAsyncComponent, onBeforeUnmount, onMounted, ref } from "vue";
import { MessageCircle, PenTool, Code, Rocket, Star } from "lucide-vue-next";

const PROCESS_ICONS = [MessageCircle, PenTool, Code, Rocket];

// Pulls gsap (the 3D-tilt/hover animation library) into its own lazily
// fetched chunk instead of bundling it into the homepage's critical initial
// JS — the "Portfolio" section it belongs to is an optional, admin-toggled,
// below-the-fold section that a given homepage config may not even render.
const AccordionGallery = defineAsyncComponent(() => import("@/Components/Public/AccordionGallery.vue"));

// Same reasoning for the hero's WebGL planet: it's decorative (the
// hero's actual text/CTAs render immediately regardless), so ogl — not
// otherwise needed on the homepage — loads in its own chunk instead of
// blocking/bloating the critical bundle.
const Planet = defineAsyncComponent(() => import("@/Components/Public/Planet.vue"));

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
        badge: project.project_type === "personal" ? "Contoh Kapabilitas" : null,
    }))
);

// HUD section numbers ("[ 03 ]") follow the admin-configured section order.
function sectionNumber(type) {
    return (props.sections || []).indexOf(type);
}

// Headline treatment from the reference: lighter lead-in words, heavier
// closing words ("INSIDE THE / TESSERACT"). Admin text, so split by count.
const heroTitleParts = computed(() => {
    const words = (props.hero?.title || "").trim().split(/\s+/).filter(Boolean);
    if (words.length < 3) return { lead: "", tail: words.join(" ") };
    const tailCount = Math.ceil(words.length / 3);
    return { lead: words.slice(0, -tailCount).join(" "), tail: words.slice(-tailCount).join(" ") };
});

// Live local clock for the hero HUD bar.
const clock = ref("");
let clockTimer = null;
function tickClock() {
    clock.value = new Date().toLocaleTimeString("id-ID", { hour: "2-digit", minute: "2-digit", second: "2-digit", timeZone: "Asia/Jakarta" }).replace(/\./g, ":");
}
onMounted(() => {
    tickClock();
    clockTimer = setInterval(tickClock, 1000);
});
onBeforeUnmount(() => clearInterval(clockTimer));

const openFaq = ref(null);
function toggleFaq(id) {
    openFaq.value = openFaq.value === id ? null : id;
}

const organizationJsonLd = computed(() => {
    const general = page.props.settings?.general || {};
    const contact = page.props.settings?.contact || {};
    const social = page.props.settings?.social || {};
    const origin = typeof window !== "undefined" ? window.location.origin : "";
    const toAbsolute = (path) => (path && !/^https?:\/\//.test(path) ? origin + path : path);

    const sameAs = [social.instagram, social.facebook, social.linkedin, social.youtube, social.tiktok].filter(Boolean);

    return {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": general.website_name,
        "url": origin,
        "logo": toAbsolute(general.logo),
        "description": general.description || general.tagline,
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": contact.phone || contact.whatsapp_number || undefined,
            "email": contact.email || undefined,
            "contactType": "customer service",
        },
        "sameAs": sameAs.length ? sameAs : undefined,
    };
});
</script>

<template>
    <SeoHead :title="page.props.settings?.general?.tagline" :json-ld="organizationJsonLd" />
    <PublicLayout theme="space">
        <LiveTicker :activities="recentActivity" variant="floating" />
        <template v-for="type in sections" :key="type">
            <!-- Hero: full-screen dot-matrix planet -->
            <section v-if="type === 'hero' && hero" class="hero space-hero force-dark relative min-h-[100svh] flex flex-col overflow-hidden bg-space-void text-white">
                <Planet class="z-0" />
                <div class="hero__scrim absolute inset-0 z-[1] pointer-events-none" aria-hidden="true"></div>

                <!-- HUD: object label beside the planet -->
                <div class="hidden md:block absolute z-[2] top-[15%] right-[5%] font-mono text-[10px] uppercase tracking-[0.22em] text-white/50" aria-hidden="true">
                    <p class="text-brand-400 mb-1">[ exo ] &mdash; 01</p>
                    <p class="text-white/80">Planet {{ page.props.settings?.general?.website_name }}</p>
                    <p>Orbit stabil &middot; 24/7 online</p>
                </div>
                <!-- HUD: vertical side label -->
                <p class="hidden lg:block absolute z-[2] right-6 top-1/2 -translate-y-1/2 [writing-mode:vertical-rl] font-mono text-[10px] uppercase tracking-[0.3em] text-white/30" aria-hidden="true">
                    Est. web &middot; design &middot; engineering
                </p>

                <div class="relative z-10 flex-1 flex items-end max-w-[1440px] w-full mx-auto px-4 md:px-8 pt-32 pb-28 md:pb-32">
                    <div class="max-w-4xl">
                        <p v-scroll-reveal class="flex items-center gap-3 font-mono text-[10px] md:text-[11px] uppercase tracking-[0.25em] text-white/55 mb-6">
                            <span class="text-brand-400">[ 00 ]</span>
                            <span class="h-px w-10 bg-white/25"></span>
                            {{ hero.eyebrow }}
                        </p>
                        <h1 v-scroll-reveal="{ delay: 80 }" class="font-display uppercase leading-[0.95] tracking-tight mb-7 [text-wrap:balance]">
                            <span v-if="heroTitleParts.lead" class="block font-light text-4xl sm:text-6xl lg:text-7xl xl:text-[5.5rem] text-white/90">{{ heroTitleParts.lead }}</span>
                            <span class="block font-extrabold text-4xl sm:text-6xl lg:text-7xl xl:text-[5.5rem] hero__title-glow">{{ heroTitleParts.tail }}</span>
                        </h1>
                        <p v-scroll-reveal="{ delay: 160 }" class="text-base md:text-lg text-white/60 leading-relaxed max-w-xl mb-10">
                            {{ hero.subtitle }}
                        </p>
                        <div v-scroll-reveal="{ delay: 240 }" class="flex flex-wrap gap-3">
                            <Button :href="hero.cta_primary_url" variant="pill" size="large">{{ hero.cta_primary_text }}</Button>
                            <Button :href="hero.cta_secondary_url" variant="glass" size="large">{{ hero.cta_secondary_text }}</Button>
                        </div>
                    </div>
                </div>

                <!-- HUD bottom bar -->
                <div class="absolute z-10 inset-x-0 bottom-0 border-t border-white/10" aria-hidden="true">
                    <div class="max-w-[1440px] mx-auto px-4 md:px-8 h-14 flex items-center justify-between gap-6 font-mono text-[10px] uppercase tracking-[0.22em] text-white/40">
                        <span class="flex items-center gap-3">
                            <span class="scroll-cue relative block w-px h-6 bg-white/15 overflow-hidden"><span class="absolute inset-x-0 top-0 h-2 bg-brand-400"></span></span>
                            Scroll untuk menjelajah
                        </span>
                        <span class="hidden md:inline">06&deg;12&prime; S &mdash; 106&deg;49&prime; E</span>
                        <span class="hidden sm:flex items-center gap-2 tabular-nums">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-400 animate-pulse motion-reduce:animate-none"></span>
                            {{ clock }} WIB
                        </span>
                    </div>
                </div>
            </section>

            <!-- About -->
            <AboutSection v-else-if="type === 'about' && about" :about="about" :index="sectionNumber('about')" />

            <!-- Services -->
            <section v-else-if="type === 'services'" class="relative max-w-[1440px] mx-auto px-4 md:px-8 py-24 md:py-32">
                <SpaceSectionHeading
                    :index="sectionNumber('services')"
                    :label="servicesSection?.subtitle || 'Layanan Kami'"
                    :title="servicesSection?.title || 'Solusi Website untuk Setiap Kebutuhan'"
                />
                <div v-for="(service, index) in services" :key="service.id" v-scroll-reveal="{ delay: index * 60 }">
                    <ServiceCard :service="service" :index="index" />
                </div>
            </section>

            <!-- Portfolio -->
            <section v-else-if="type === 'portfolio' && portfolioProjects?.length" class="relative py-24 md:py-32">
                <div class="max-w-[1440px] mx-auto px-4 md:px-8">
                    <SpaceSectionHeading
                        :index="sectionNumber('portfolio')"
                        :label="portfolioSection?.subtitle || 'Portfolio'"
                        :title="portfolioSection?.title || 'Hasil Karya Kami'"
                    />
                    <div v-scroll-reveal="{ delay: 100 }">
                        <AccordionGallery :items="portfolioGalleryItems" overlay-color="#05060a" accent-color="#a3e635" :height="480" />
                    </div>
                    <div class="mt-10 flex justify-end">
                        <Button variant="glass" href="/portfolio">Lihat semua portfolio</Button>
                    </div>
                </div>
            </section>

            <!-- Why Us -->
            <section v-else-if="type === 'why_us' && whyUs" class="relative py-24 md:py-32">
                <div class="max-w-[1440px] mx-auto px-4 md:px-8">
                    <SpaceSectionHeading :index="sectionNumber('why_us')" :label="whyUs.subtitle" :title="whyUs.title" />
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
                        <div v-for="(item, index) in whyUs.items" :key="item.title" v-scroll-reveal="{ delay: index * 80 }">
                            <WhyUsCard :title="item.title" :description="item.description" :icon="item.icon" :index="index" dark />
                        </div>
                    </div>
                </div>
            </section>

            <!-- Process: orbit rings -->
            <section v-else-if="type === 'process' && process" class="relative py-24 md:py-36 overflow-hidden">
                <div class="orbits absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 pointer-events-none" aria-hidden="true">
                    <span v-for="n in 6" :key="n" class="orbit" :style="{ '--i': n }"></span>
                </div>
                <div class="relative max-w-[1440px] mx-auto px-4 md:px-8">
                    <SpaceSectionHeading :index="sectionNumber('process')" :label="process.subtitle" :title="process.title" center />
                    <div class="relative">
                        <div class="hidden md:block absolute top-8 left-[12.5%] right-[12.5%] border-t border-dashed border-white/20"></div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-10 md:gap-6">
                            <div v-for="(step, index) in process.steps" :key="step.title" v-scroll-reveal="{ delay: index * 100 }" class="relative text-center px-4">
                                <div class="process-node relative w-16 h-16 mx-auto mb-6 rounded-full border border-white/15 bg-space-deep flex items-center justify-center text-brand-400">
                                    <component :is="PROCESS_ICONS[index]" class="w-6 h-6" aria-hidden="true" />
                                </div>
                                <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400 mb-3">Step {{ String(index + 1).padStart(2, "0") }}</p>
                                <h3 class="font-display font-semibold text-lg text-white tracking-tight mb-2">{{ step.title }}</h3>
                                <p class="text-sm text-white/55 leading-relaxed max-w-xs mx-auto">{{ step.description }}</p>
                            </div>
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
                :index="sectionNumber('pricing')"
            />

            <!-- Testimonials -->
            <section v-else-if="type === 'testimonials'" class="max-w-[1440px] mx-auto px-4 md:px-8 py-24 md:py-32">
                <SpaceSectionHeading
                    :index="sectionNumber('testimonials')"
                    :label="testimonialsSection?.subtitle || 'Testimoni'"
                    :title="testimonialsSection?.title || 'Apa Kata Klien Kami'"
                />
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 md:gap-5">
                    <figure
                        v-for="(t, index) in testimonials"
                        :key="t.id"
                        v-scroll-reveal="{ delay: index * 80 }"
                        class="relative flex flex-col rounded-2xl border border-white/10 bg-white/[0.03] p-7 md:p-8"
                    >
                        <span class="absolute right-6 top-3 font-display text-7xl leading-none text-white/[0.06]" aria-hidden="true">&rdquo;</span>
                        <div class="flex gap-1 mb-6" aria-hidden="true">
                            <Star v-for="n in 5" :key="n" class="w-3.5 h-3.5" :class="n <= t.rating ? 'fill-brand-400 text-brand-400' : 'text-white/15'" />
                        </div>
                        <blockquote class="text-white/80 leading-relaxed mb-8 flex-1">&ldquo;{{ t.testimonial }}&rdquo;</blockquote>
                        <figcaption class="pt-5 border-t border-white/10">
                            <p class="font-display font-semibold text-sm text-white">{{ t.name }}</p>
                            <p class="font-mono text-[10px] uppercase tracking-[0.18em] text-white/40 mt-1">{{ t.position }}<span v-if="t.company"> / {{ t.company }}</span></p>
                        </figcaption>
                    </figure>
                </div>
            </section>

            <!-- FAQ -->
            <section v-else-if="type === 'faq'" class="py-24 md:py-32">
                <div class="max-w-[1440px] mx-auto px-4 md:px-8 grid grid-cols-1 lg:grid-cols-12 gap-10">
                    <div class="lg:col-span-5">
                        <SpaceSectionHeading
                            :index="sectionNumber('faq')"
                            :label="faqSection?.subtitle || 'FAQ'"
                            :title="faqSection?.title || 'Pertanyaan yang Sering Diajukan'"
                        />
                    </div>
                    <div class="lg:col-span-7 border-t border-white/10">
                        <div v-for="(faq, index) in faqs" :key="faq.id" v-scroll-reveal="{ delay: index * 60 }" class="border-b border-white/10">
                            <button
                                @click="toggleFaq(faq.id)"
                                :aria-expanded="openFaq === faq.id"
                                :aria-controls="'home-faq-answer-' + faq.id"
                                class="group w-full flex items-center gap-5 py-6 text-left"
                            >
                                <span class="font-mono text-[10px] tracking-[0.2em] text-white/35 shrink-0">{{ String(index + 1).padStart(2, "0") }}</span>
                                <span class="flex-1 font-semibold text-white/85 transition-colors group-hover:text-brand-300">{{ faq.question }}</span>
                                <span
                                    aria-hidden="true"
                                    class="w-8 h-8 shrink-0 rounded-full border border-white/15 flex items-center justify-center text-brand-400 transition-transform duration-300"
                                    :class="openFaq === faq.id ? 'rotate-45 bg-white/5' : ''"
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
                                <div v-if="openFaq === faq.id" :id="'home-faq-answer-' + faq.id" class="pb-6 pl-10 pr-12 text-white/55 leading-relaxed">{{ faq.answer }}</div>
                            </Transition>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Blog -->
            <section v-else-if="type === 'blog' && latestPosts?.length" class="py-24 md:py-32">
                <div class="max-w-[1440px] mx-auto px-4 md:px-8">
                    <SpaceSectionHeading
                        :index="sectionNumber('blog')"
                        :label="blogSection?.subtitle || 'Blog'"
                        :title="blogSection?.title || 'Artikel & Insight Terbaru'"
                    />
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 md:gap-8">
                        <article v-for="(post, index) in latestPosts" :key="post.id" v-scroll-reveal="{ delay: index * 80 }" class="group">
                            <Link :href="'/blog/' + post.slug" class="block">
                                <div v-image-reveal class="aspect-[5/3] rounded-xl overflow-hidden bg-space-panel ring-1 ring-white/10 mb-5">
                                    <img
                                        v-if="post.featured_image"
                                        :src="post.featured_image"
                                        :alt="post.title"
                                        width="400"
                                        height="240"
                                        loading="lazy"
                                        class="w-full h-full object-cover opacity-85 transition-all duration-500 group-hover:scale-105 group-hover:opacity-100"
                                    />
                                </div>
                                <div class="flex items-center gap-3 font-mono text-[10px] uppercase tracking-[0.18em] text-white/40 mb-3">
                                    <span v-if="post.category" class="text-brand-400">{{ post.category.name }}</span>
                                    <span>{{ formatDate(post.published_at) }}</span>
                                </div>
                                <h3 class="font-display font-semibold text-lg text-white tracking-tight line-clamp-2 transition-colors group-hover:text-brand-300">{{ post.title }}</h3>
                            </Link>
                        </article>
                    </div>
                    <div class="mt-12 flex justify-end">
                        <Button variant="glass" href="/blog">Lihat semua artikel</Button>
                    </div>
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
/* Hero: darken the left (text side) and fade the bottom into the page void. */
.hero__scrim {
    background:
        linear-gradient(90deg, rgb(var(--space-void) / 0.85) 0%, rgb(var(--space-void) / 0.35) 45%, transparent 70%),
        linear-gradient(0deg, #05060a 0%, transparent 30%);
}
@media (max-width: 767px) {
    .hero__scrim {
        background: linear-gradient(0deg, #05060a 15%, rgb(var(--space-void) / 0.55) 55%, rgb(var(--space-void) / 0.2) 100%);
    }
}
.hero__title-glow {
    background: linear-gradient(180deg, #ffffff 30%, #d9f99d 140%);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    filter: drop-shadow(0 0 30px rgb(var(--lime-400) / 0.18));
}

.scroll-cue > span {
    animation: scroll-cue 1.8s cubic-bezier(0.65, 0, 0.35, 1) infinite;
}
@keyframes scroll-cue {
    from { transform: translateY(-100%); }
    to { transform: translateY(300%); }
}

/* Process: slowly counter-rotating dashed orbits behind the steps. */
.orbits {
    width: min(1100px, 160vw);
    aspect-ratio: 1;
}
.orbit {
    position: absolute;
    inset: calc(var(--i) * 7%);
    border-radius: 9999px;
    border: 1px dashed rgba(255, 255, 255, calc(0.1 - var(--i) * 0.012));
    animation: orbit-spin calc(40s + var(--i) * 12s) linear infinite;
}
.orbit:nth-child(even) {
    animation-direction: reverse;
    border-style: solid;
    border-color: rgb(var(--lime-400) / 0.06);
}
.orbit::after {
    content: "";
    position: absolute;
    top: -3px;
    left: 50%;
    width: 5px;
    height: 5px;
    border-radius: 9999px;
    background: #a3e635;
    box-shadow: 0 0 12px #a3e635;
    opacity: 0.7;
}
@keyframes orbit-spin {
    to { transform: rotate(360deg); }
}

.process-node {
    box-shadow: 0 0 0 6px #05060a, 0 0 40px -6px rgb(var(--lime-400) / 0.35);
}

@media (prefers-reduced-motion: reduce) {
    .scroll-cue > span,
    .orbit {
        animation: none;
    }
}
</style>
