<script setup>
/**
 * Resolves a kebab-case icon name stored as a plain string in the DB
 * (e.g. "life-buoy", "dollar-sign") to the matching lucide-vue-next
 * component (e.g. LifeBuoy, DollarSign) and renders it. Falls back to a
 * generic sparkle icon if the name is empty/unrecognized, so a bad/blank
 * admin-entered value never breaks the page.
 */
import { computed } from "vue";
import {
    Palette,
    Search,
    LifeBuoy,
    DollarSign,
    Store,
    Building2,
    ShoppingCart,
    Code,
    ShieldCheck,
    Rocket,
    Zap,
    Smartphone,
    Users,
    Headphones,
    TrendingUp,
    Clock,
    CheckCircle,
    Star,
    Globe,
    Layout,
    Settings,
    Heart,
    Target,
    Compass,
    Sparkles,
} from "lucide-vue-next";

// Named imports only (not `import * as icons`) — this component renders
// icons chosen from the curated dropdown in constants/icons.js, and a
// wildcard import would pull all ~1500 lucide icons into the bundle since
// Rollup can't tree-shake a dynamic `icons[key]` lookup. Add new entries
// here (and to constants/icons.js) together if the curated list grows.
const ICONS = {
    Palette,
    Search,
    LifeBuoy,
    DollarSign,
    Store,
    Building2,
    ShoppingCart,
    Code,
    ShieldCheck,
    Rocket,
    Zap,
    Smartphone,
    Users,
    Headphones,
    TrendingUp,
    Clock,
    CheckCircle,
    Star,
    Globe,
    Layout,
    Settings,
    Heart,
    Target,
    Compass,
};

const props = defineProps({
    name: { type: String, default: "" },
});

function toPascalCase(kebab) {
    return kebab
        .split("-")
        .filter(Boolean)
        .map((part) => part.charAt(0).toUpperCase() + part.slice(1))
        .join("");
}

const resolved = computed(() => {
    const key = toPascalCase(props.name || "");
    return ICONS[key] || Sparkles;
});
</script>

<template>
    <component :is="resolved" />
</template>
