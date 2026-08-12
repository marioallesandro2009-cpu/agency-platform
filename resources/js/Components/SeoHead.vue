<script setup>
import { Head, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    title: { type: String, default: "" },
    description: { type: String, default: "" },
    image: { type: String, default: "" },
    type: { type: String, default: "website" },
    jsonLd: { type: [Object, Array], default: null },
});

const page = usePage();

const siteName = computed(() => page.props.settings?.general?.website_name || "Website");
const fullTitle = computed(() => (props.title ? `${props.title} | ${siteName.value}` : siteName.value));
const metaDescription = computed(() => props.description || page.props.settings?.general?.description || "");
const ogImage = computed(() => props.image || page.props.settings?.general?.og_image || "");
const canonicalUrl = computed(() => {
    const origin = typeof window !== "undefined" ? window.location.origin : "";
    return origin + page.url.split("?")[0];
});

// JSON.stringify does not escape "<", so a title/excerpt containing a closing
// script tag could break out of it. Escaping "<" keeps the JSON valid and safe.
const jsonLdString = computed(() => (props.jsonLd ? JSON.stringify(props.jsonLd).replace(/</g, "\\u003c") : ""));
</script>

<template>
    <Head>
        <title>{{ fullTitle }}</title>
        <meta name="description" :content="metaDescription" />
        <link rel="canonical" :href="canonicalUrl" />

        <meta property="og:site_name" :content="siteName" />
        <meta property="og:title" :content="fullTitle" />
        <meta property="og:description" :content="metaDescription" />
        <meta property="og:type" :content="type" />
        <meta property="og:url" :content="canonicalUrl" />
        <meta v-if="ogImage" property="og:image" :content="ogImage" />

        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="fullTitle" />
        <meta name="twitter:description" :content="metaDescription" />
        <meta v-if="ogImage" name="twitter:image" :content="ogImage" />

        <component v-if="jsonLdString" :is="'script'" type="application/ld+json">{{ jsonLdString }}</component>
    </Head>
</template>
