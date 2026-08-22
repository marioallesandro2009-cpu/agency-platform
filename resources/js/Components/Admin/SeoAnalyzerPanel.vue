<script setup>
import { computed } from "vue";
import { analyzeSeo, seoScoreLabel, scoreDotColor, scorePanelColor, checkDotColor } from "@/utils/seoAnalyzer";

const props = defineProps({
    title: { type: String, default: "" },
    slug: { type: String, default: "" },
    content: { type: String, default: "" },
    seoTitle: { type: String, default: "" },
    seoDescription: { type: String, default: "" },
    focusKeyword: { type: String, default: "" },
});

const analysis = computed(() =>
    analyzeSeo({
        title: props.title,
        slug: props.slug,
        content: props.content,
        seoTitle: props.seoTitle,
        seoDescription: props.seoDescription,
        focusKeyword: props.focusKeyword,
    })
);

// Maps each of the general (non-keyword) checklist sections to the check
// ids analyzeSeo() produces for it. Keyword-specific checks (title/
// description/slug/intro/heading placement) are rendered separately below
// under "Focus Keyword" so that requirement reads as one unified checklist
// instead of being scattered across the sections that also own them.
const SECTIONS = [
    { title: "Title Tag", ids: ["title_length"] },
    { title: "Meta Description", ids: ["description_length"] },
    { title: "URL Slug", ids: ["slug_quality"] },
    { title: "Struktur Heading", ids: ["heading_presence", "heading_hierarchy"] },
    { title: "Alt Text Gambar", ids: ["image_alt"] },
    { title: "Internal Linking", ids: ["internal_links"] },
    { title: "External Linking", ids: ["external_links"] },
    { title: "Keyword Density", ids: ["keyword_density"] },
    { title: "Readability", ids: ["readability", "content_length"] },
];

const KEYWORD_CHECK_IDS = ["keyword_title", "keyword_description", "keyword_slug", "keyword_intro", "keyword_heading"];

function checksFor(ids) {
    return analysis.value.checks.filter((check) => ids.includes(check.id));
}

const sections = computed(() =>
    SECTIONS.map((section) => ({ title: section.title, checks: checksFor(section.ids) })).filter((section) => section.checks.length > 0)
);

const keywordChecks = computed(() => checksFor(KEYWORD_CHECK_IDS));
</script>

<template>
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <div class="flex items-center justify-between mb-3">
            <p class="text-sm font-semibold text-gray-800">SEO Analyzer</p>
            <span class="text-xs text-gray-400">~{{ analysis.readingTime }} menit baca</span>
        </div>

        <div class="flex items-center justify-between border rounded-md px-3 py-2.5 mb-4" :class="scorePanelColor(analysis.score)">
            <p class="text-sm font-semibold text-gray-800">Skor SEO</p>
            <span class="inline-flex items-center gap-2 text-sm font-bold text-gray-800">
                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" :class="scoreDotColor(analysis.score)"></span>
                {{ analysis.score }}/100 - {{ seoScoreLabel(analysis.score) }}
            </span>
        </div>

        <div class="space-y-4">
            <div v-for="section in sections" :key="section.title">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">{{ section.title }}</p>
                <ul class="space-y-1.5">
                    <li v-for="check in section.checks" :key="check.id" class="text-xs text-gray-600 flex items-start gap-2">
                        <span class="w-2 h-2 rounded-full mt-1 flex-shrink-0" :class="checkDotColor(check.status)"></span>
                        <span>{{ check.message }}</span>
                    </li>
                </ul>
            </div>

            <div>
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Focus Keyword</p>
                <ul v-if="analysis.hasKeyword" class="space-y-1.5">
                    <li v-for="check in keywordChecks" :key="check.id" class="text-xs text-gray-600 flex items-start gap-2">
                        <span class="w-2 h-2 rounded-full mt-1 flex-shrink-0" :class="checkDotColor(check.status)"></span>
                        <span>{{ check.message }}</span>
                    </li>
                </ul>
                <p v-else class="text-xs text-gray-400">
                    Isi focus keyword untuk mengaktifkan checklist ini (kemunculan di title, meta description, slug, paragraf pertama, dan subheading).
                </p>
            </div>

            <div class="border-t border-gray-100 pt-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1.5">Technical SEO (otomatis)</p>
                <ul class="space-y-1.5">
                    <li class="text-xs text-gray-500 flex items-start gap-2">
                        <span class="w-2 h-2 rounded-full mt-1 flex-shrink-0 bg-green-500"></span>
                        <span>Canonical URL self-referencing otomatis aktif di halaman publik.</span>
                    </li>
                    <li class="text-xs text-gray-500 flex items-start gap-2">
                        <span class="w-2 h-2 rounded-full mt-1 flex-shrink-0 bg-green-500"></span>
                        <span>Open Graph &amp; Twitter Card otomatis dari title, meta description, dan featured image.</span>
                    </li>
                    <li class="text-xs text-gray-500 flex items-start gap-2">
                        <span class="w-2 h-2 rounded-full mt-1 flex-shrink-0 bg-green-500"></span>
                        <span>Schema markup Article (BlogPosting) otomatis tampil di halaman publik artikel.</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
