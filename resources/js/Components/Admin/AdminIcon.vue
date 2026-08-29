<script setup>
defineProps({
    name: { type: String, required: true },
});

// Small hand-drawn icon set (24x24, stroke-based) — kept intentionally
// simple/geometric rather than matching any specific icon library, so every
// path here is unambiguous and safe to hand-maintain.
const paths = {
    dashboard: 'M4 4h7v7H4V4zm9 0h7v4h-7V4zm0 7h7v9h-7v-9zM4 14h7v6H4v-6z',
    services: 'M9 7V5a2 2 0 012-2h2a2 2 0 012 2v2m-9 0h14a1 1 0 011 1v9a2 2 0 01-2 2H6a2 2 0 01-2-2V8a1 1 0 011-1z',
    sections: 'M4 5h16M4 10h16M4 15h10M4 20h10',
    about: 'M12 22a10 10 0 100-20 10 10 0 000 20zM12 8h.01 M11 12h1v6h1',
    portfolio: 'M4 5h16v14H4V5zm2 11l4-5 3 3 3-4 4 6H6z M8 9a1 1 0 100-2 1 1 0 000 2z',
    pricing: 'M4 4h6l10 10-6 6L4 10V4zm4 3a1 1 0 100-2 1 1 0 000 2z',
    maintenance: 'M21 7l-4 4-3-3 4-4a5 5 0 00-6.5 6.5L4 17v3h3l6.5-6.5A5 5 0 0021 7z',
    testimonials: 'M4 4h16v11H8l-4 4V4z',
    faq: 'M12 22a10 10 0 100-20 10 10 0 000 20zM9.5 9a2.5 2.5 0 015 .3c0 1.7-2.5 1.7-2.5 3.7 M12 16.5h.01',
    blog: 'M6 3h9l4 4v14H6V3zm8 0v5h5 M9 12h7 M9 16h7 M9 8h3',
    leads: 'M8 12a3 3 0 100-6 3 3 0 000 6zm8 0a3 3 0 100-6 3 3 0 000 6zM2 20a6 6 0 0112 0M14 20a6 6 0 0110-1.5',
    media: 'M4 5h16v14H4V5zm4 4a1 1 0 100-2 1 1 0 000 2z M4 17l5-5 4 4 3-3 4 4',
    mascot: 'M12 22a10 10 0 100-20 10 10 0 000 20z M9 10h.01 M15 10h.01 M8 15a4 4 0 008 0',
    navigation: 'M4 6h16M4 6l2-2m-2 2l2 2 M4 12h16M4 12l2-2m-2 2l2 2 M4 18h16M4 18l2-2m-2 2l2 2',
    techstack: 'M12 2L2 7L12 12L22 7L12 2Z M2 12L12 17L22 12 M2 17L12 22L22 17',
    settings: 'M12 15a3 3 0 100-6 3 3 0 000 6z M19 12a7 7 0 00-.1-1.2l2-1.6-2-3.4-2.4 1a7 7 0 00-2-1.2L14 3h-4l-.5 2.6a7 7 0 00-2 1.2l-2.4-1-2 3.4 2 1.6A7 7 0 005 12c0 .4 0 .8.1 1.2l-2 1.6 2 3.4 2.4-1c.6.5 1.3.9 2 1.2L10 21h4l.5-2.6c.7-.3 1.4-.7 2-1.2l2.4 1 2-3.4-2-1.6c.1-.4.1-.8.1-1.2z',
};
</script>

<template>
    <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" :d="paths[name] || paths.dashboard" />
    </svg>
</template>
