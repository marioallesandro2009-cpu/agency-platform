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
const origin = computed(() => (typeof window !== "undefined" ? window.location.origin : ""));
const ogImage = computed(() => {
    const image = props.image || page.props.settings?.general?.og_image || "";
    if (image && !/^https?:\/\//.test(image)) {
        return origin.value + image;
    }
    return image;
});
const canonicalUrl = computed(() => origin.value + page.url.split("?")[0]);

// JSON.stringify does not escape "<", so a title/excerpt containing a closing
// script tag could break out of it. Escaping "<" keeps the JSON valid and safe.
const jsonLdString = computed(() => (props.jsonLd ? JSON.stringify(props.jsonLd).replace(/</g, "\\u003c") : ""));
</script>

<template>
    <Head>
        <title>{{ fullTitle }}</title>
        <meta head-key="description" name="description" :content="metaDescription" />
        <link head-key="canonical" rel="canonical" :href="canonicalUrl" />

        <meta head-key="og-site-name" property="og:site_name" :content="siteName" />
        <meta head-key="og-title" property="og:title" :content="fullTitle" />
        <meta head-key="og-description" property="og:description" :content="metaDescription" />
        <meta head-key="og-type" property="og:type" :content="type" />
        <meta head-key="og-url" property="og:url" :content="canonicalUrl" />
        <meta v-if="ogImage" head-key="og-image" property="og:image" :content="ogImage" />

        <meta head-key="twitter-card" name="twitter:card" content="summary_large_image" />
        <meta head-key="twitter-title" name="twitter:title" :content="fullTitle" />
        <meta head-key="twitter-description" name="twitter:description" :content="metaDescription" />
        <meta v-if="ogImage" head-key="twitter-image" name="twitter:image" :content="ogImage" />

        <component v-if="jsonLdString" head-key="json-ld" :is="'script'" type="application/ld+json" :nonce="page.props.cspNonce">{{ jsonLdString }}</component>
    </Head>
</template>
