<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed, ref } from "vue";

const page = usePage();
const settings = page.props.settings || {};
const navServices = page.props.navServices || [];
const homeItem = computed(() => (page.props.navMenu || []).find((item) => item.url === "/"));
const menu = computed(() => (page.props.navMenu || []).filter((item) => item.url !== "/"));
const mobileMenuOpen = ref(false);
const servicesDropdownOpen = ref(false);
const mobileServicesOpen = ref(false);

function closeServicesDropdownIfFocusLeft(event) {
    if (!event.currentTarget.contains(event.relatedTarget)) {
        servicesDropdownOpen.value = false;
    }
}

const organizationJsonLd = computed(() => {
    const origin = typeof window !== "undefined" ? window.location.origin : "";
    const sameAs = Object.values(settings.social || {}).filter(Boolean);

    return JSON.stringify({
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": settings.general?.website_name,
        "description": settings.general?.description,
        "url": origin,
        "logo": settings.general?.logo || undefined,
        "email": settings.contact?.email || undefined,
        "telephone": settings.contact?.phone || undefined,
        "address": settings.contact?.address ? { "@type": "PostalAddress", "addressLocality": settings.contact.address } : undefined,
        "sameAs": sameAs.length ? sameAs : undefined,
    }).replace(/</g, "\\u003c");
});

const year = new Date().getFullYear();
</script>

<template>
    <Head>
        <component :is="'script'" type="application/ld+json">{{ organizationJsonLd }}</component>
    </Head>
    <div class="min-h-screen flex flex-col bg-white">
        <div class="bg-charcoal text-gray-300 text-xs">
            <div class="max-w-7xl mx-auto px-4 py-2 flex flex-col sm:flex-row justify-between gap-1">
                <div class="flex gap-4">
                    <span v-if="settings.contact?.phone">Telp: {{ settings.contact.phone }}</span>
                    <span v-if="settings.contact?.email">Email: {{ settings.contact.email }}</span>
                </div>
                <div class="flex gap-3">
                    <a v-if="settings.social?.instagram" :href="settings.social.instagram" target="_blank" class="hover:text-white">Instagram</a>
                    <a v-if="settings.social?.facebook" :href="settings.social.facebook" target="_blank" class="hover:text-white">Facebook</a>
                    <a v-if="settings.social?.linkedin" :href="settings.social.linkedin" target="_blank" class="hover:text-white">LinkedIn</a>
                </div>
            </div>
        </div>

        <header class="sticky top-0 z-40 bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
                <Link href="/" class="flex items-center gap-2">
                    <img v-if="settings.general?.logo" :src="settings.general.logo" class="h-8" :alt="settings.general?.website_name" />
                    <span v-else class="font-bold text-xl text-gray-900">{{ settings.general?.website_name }}</span>
                </Link>

                <nav class="hidden md:flex items-center gap-6">
                    <Link v-if="homeItem" :href="homeItem.url" class="nav-underline text-sm font-medium text-gray-600 hover:text-brand-600">{{ homeItem.label }}</Link>

                    <div
                        class="relative"
                        @mouseenter="servicesDropdownOpen = true"
                        @mouseleave="servicesDropdownOpen = false"
                        @focusin="servicesDropdownOpen = true"
                        @focusout="closeServicesDropdownIfFocusLeft"
                    >
                        <button
                            :aria-expanded="servicesDropdownOpen"
                            aria-controls="services-dropdown"
                            class="nav-underline flex items-center gap-1 text-sm font-medium text-gray-600 hover:text-brand-600"
                        >
                            Jasa &amp; Layanan
                            <span aria-hidden="true" class="text-xs">v</span>
                        </button>
                        <div v-if="servicesDropdownOpen" id="services-dropdown" class="absolute left-0 top-full w-72 bg-white shadow-lg rounded-lg border border-gray-100 py-2">
                            <Link
                                v-for="service in navServices"
                                :key="service.id"
                                :href="'/' + service.slug"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand-600"
                            >
                                {{ service.name }}
                            </Link>
                        </div>
                    </div>

                    <Link
                        v-for="item in menu"
                        :key="item.id"
                        :href="item.url"
                        class="nav-underline text-sm font-medium text-gray-600 hover:text-brand-600"
                    >
                        {{ item.label }}
                    </Link>
                </nav>

                                <a v-if="settings.contact?.whatsapp_number" :href="'https://wa.me/' + settings.contact.whatsapp_number" target="_blank" class="hidden md:inline-block bg-brand-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-brand-700">Konsultasi Gratis</a>

                <button
                    @click="mobileMenuOpen = !mobileMenuOpen"
                    :aria-expanded="mobileMenuOpen"
                    aria-controls="mobile-menu-panel"
                    aria-label="Menu"
                    class="md:hidden text-gray-700 p-1"
                >
                    <svg v-if="!mobileMenuOpen" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg v-else class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div v-if="mobileMenuOpen" id="mobile-menu-panel" class="md:hidden border-t px-4 py-3 space-y-1">
                <Link v-if="homeItem" :href="homeItem.url" class="block text-sm font-medium text-gray-700 py-1.5">{{ homeItem.label }}</Link>

                <button
                    @click="mobileServicesOpen = !mobileServicesOpen"
                    :aria-expanded="mobileServicesOpen"
                    aria-controls="mobile-services-panel"
                    class="w-full flex items-center justify-between text-sm font-medium text-gray-700 py-1.5"
                >
                    Jasa &amp; Layanan
                    <span aria-hidden="true" class="text-xs">{{ mobileServicesOpen ? "^" : "v" }}</span>
                </button>
                <div v-if="mobileServicesOpen" id="mobile-services-panel" class="pl-3 space-y-1 border-l ml-1">
                    <Link
                        v-for="service in navServices"
                        :key="service.id"
                        :href="'/' + service.slug"
                        class="block text-sm text-gray-600 py-1"
                    >
                        {{ service.name }}
                    </Link>
                </div>

                <Link
                    v-for="item in menu"
                    :key="item.id"
                    :href="item.url"
                    class="block text-sm font-medium text-gray-700 py-1.5"
                >
                    {{ item.label }}
                </Link>
            </div>
        </header>

        <main class="flex-1">
            <slot />
        </main>

        <a
            v-if="settings.contact?.whatsapp_number"
            :href="'https://wa.me/' + settings.contact.whatsapp_number"
            target="_blank"
            rel="noopener"
            aria-label="Chat WhatsApp"
            class="fixed bottom-5 right-5 z-50 flex h-14 w-14 items-center justify-center rounded-full bg-[#25D366] text-white shadow-lg hover:bg-[#1ebe57] transition-colors"
        >
            <svg viewBox="0 0 32 32" class="h-8 w-8" fill="currentColor" aria-hidden="true">
                <path d="M16.001 3C9.373 3 4 8.373 4 15c0 2.31.64 4.47 1.75 6.31L4 29l7.86-1.71A11.94 11.94 0 0 0 16.001 27C22.63 27 28 21.627 28 15S22.63 3 16.001 3Zm0 21.75c-1.93 0-3.73-.53-5.28-1.45l-.38-.22-4.66 1.01 1.02-4.53-.25-.4A9.72 9.72 0 0 1 5.25 15c0-5.93 4.82-10.75 10.75-10.75S26.75 9.07 26.75 15 21.93 24.75 16.001 24.75Zm5.98-8.09c-.33-.16-1.93-.95-2.23-1.06-.3-.11-.52-.16-.74.16-.22.33-.85 1.06-1.04 1.27-.19.22-.38.24-.71.08-.33-.16-1.38-.51-2.63-1.62-.97-.87-1.63-1.94-1.82-2.27-.19-.33-.02-.5.14-.67.15-.15.33-.38.49-.57.16-.19.22-.33.33-.55.11-.22.05-.41-.03-.57-.08-.16-.74-1.78-1.01-2.44-.27-.64-.54-.55-.74-.56-.19-.01-.41-.01-.63-.01-.22 0-.57.08-.87.41-.3.33-1.14 1.11-1.14 2.71 0 1.6 1.17 3.14 1.33 3.36.16.22 2.3 3.51 5.57 4.92.78.34 1.38.54 1.86.69.78.25 1.49.21 2.05.13.63-.09 1.93-.79 2.2-1.55.27-.76.27-1.41.19-1.55-.08-.14-.3-.22-.63-.38Z"/>
            </svg>
        </a>

        <footer class="bg-charcoal text-gray-400">
            <div class="max-w-7xl mx-auto px-4 py-12 grid grid-cols-1 md:grid-cols-4 gap-8">
                <div>
                    <h3 class="text-white font-bold text-lg mb-3">{{ settings.general?.website_name }}</h3>
                    <p class="text-sm">{{ settings.general?.description }}</p>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3 text-sm">Layanan</h4>
                    <ul class="space-y-2 text-sm">
                        <li v-for="service in navServices" :key="service.id">
                            <Link :href="'/' + service.slug" class="hover:text-white">{{ service.name }}</Link>
                        </li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3 text-sm">Kontak</h4>
                    <ul class="space-y-2 text-sm">
                        <li v-if="settings.contact?.address">{{ settings.contact.address }}</li>
                        <li v-if="settings.contact?.phone">{{ settings.contact.phone }}</li>
                        <li v-if="settings.contact?.email">{{ settings.contact.email }}</li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-3 text-sm">Ikuti Kami</h4>
                    <div class="flex gap-3 text-sm">
                        <a v-if="settings.social?.instagram" :href="settings.social.instagram" target="_blank" class="hover:text-white">Instagram</a>
                        <a v-if="settings.social?.facebook" :href="settings.social.facebook" target="_blank" class="hover:text-white">Facebook</a>
                    </div>
                </div>
            </div>
            <div class="border-t border-gray-800 py-4 text-center text-xs space-y-1">
                <p>(c) {{ year }} {{ settings.general?.website_name }}. All rights reserved.</p>
                <p class="text-gray-500">Beberapa elemen visual terinspirasi dari sumber ilustrasi umum di internet.</p>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Center-out underline hover effect for the main nav links */
.nav-underline {
    position: relative;
}
.nav-underline::after {
    content: "";
    display: block;
    position: absolute;
    left: 50%;
    bottom: -6px;
    width: 0;
    height: 2px;
    background: #4d7c0f;
    transition: width 0.3s ease, left 0.3s ease;
}
.nav-underline:hover::after,
.nav-underline:focus-visible::after {
    width: 100%;
    left: 0;
}
</style>