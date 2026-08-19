<script setup>
import { Head, Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const page = usePage();

const props = defineProps({
    // [{ label, href }] — href omitted (or absent) on the last/current item.
    items: { type: Array, required: true },
    // "light" (default) — for a plain white/light section background.
    // "dark" — for use on top of a dark hero/Scanner background: white
    // links, brand-green current item, no container padding of its own
    // (the parent hero controls horizontal padding/max-width).
    variant: { type: String, default: "light" },
});

const jsonLd = computed(() => {
    const origin = typeof window !== "undefined" ? window.location.origin : "";

    return JSON.stringify({
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": props.items.map((item, index) => ({
            "@type": "ListItem",
            "position": index + 1,
            "name": item.label,
            "item": item.href ? origin + item.href : undefined,
        })),
    }).replace(/</g, "\\u003c");
});
</script>

<template>
    <Head>
        <component :is="'script'" type="application/ld+json" :nonce="page.props.cspNonce">{{ jsonLd }}</component>
    </Head>
    <nav
        aria-label="Breadcrumb"
        :class="variant === 'dark' ? 'mb-6' : 'max-w-6xl mx-auto px-4 pt-6'"
    >
        <ol class="flex flex-wrap items-center gap-1.5 text-sm" :class="variant === 'dark' ? 'text-gray-300' : 'text-gray-500'">
            <li v-for="(item, index) in items" :key="index" class="flex items-center gap-1.5">
                <Link
                    v-if="item.href"
                    :href="item.href"
                    :class="variant === 'dark' ? 'hover:text-white' : 'hover:text-brand-600'"
                >
                    {{ item.label }}
                </Link>
                <span
                    v-else
                    class="font-medium"
                    :class="variant === 'dark' ? 'text-brand-400' : 'text-gray-700'"
                    aria-current="page"
                >
                    {{ item.label }}
                </span>
                <svg
                    v-if="index < items.length - 1"
                    class="w-3.5 h-3.5 flex-shrink-0"
                    :class="variant === 'dark' ? 'text-gray-500' : 'text-gray-300'"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                    aria-hidden="true"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
                </svg>
            </li>
        </ol>
    </nav>
</template>
