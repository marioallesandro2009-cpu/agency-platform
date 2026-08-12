<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    reverse: { type: Boolean, default: false },
    eyebrow: { type: String, default: "" },
    title: { type: String, required: true },
    description: { type: String, required: true },
    panelLinkText: { type: String, default: "" },
    panelLinkHref: { type: String, default: "" },
});

const page = usePage();
const websiteName = computed(() => page.props.settings?.general?.website_name || "ExodineWeb");
const logo = computed(() => page.props.settings?.general?.logo || null);
</script>

<template>
    <div class="min-h-screen flex bg-white" :class="reverse ? 'lg:flex-row-reverse' : ''">
        <!-- Form column -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center px-6 sm:px-12 lg:px-20 py-12">
            <Link href="/" class="inline-flex items-center gap-2 mb-10 w-fit">
                <img v-if="logo" :src="logo" :alt="websiteName" class="h-8 w-auto" />
                <span class="font-bold text-gray-900">{{ websiteName }}</span>
            </Link>

            <Transition appear name="auth-form">
                <div class="w-full max-w-sm mx-auto">
                    <slot />
                </div>
            </Transition>
        </div>

        <!-- Brand panel -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-charcoal text-white">
            <div class="absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-brand-600/40 blur-3xl motion-safe:animate-blob"></div>
                <div class="absolute bottom-[-6rem] right-[-6rem] w-[28rem] h-[28rem] rounded-full bg-brand-400/20 blur-3xl motion-safe:animate-blob [animation-delay:4s]"></div>
                <div
                    class="absolute inset-0 opacity-[0.05]"
                    style="background-image:radial-gradient(circle,#fff 1px,transparent 1px);background-size:28px 28px"
                ></div>
            </div>

            <div class="relative z-10 flex flex-col justify-center px-14 xl:px-20 max-w-xl">
                <Link href="/" class="inline-flex items-center gap-2 mb-12 w-fit group">
                    <span class="font-bold text-lg">{{ websiteName }}</span>
                </Link>

                <p
                    v-if="eyebrow"
                    class="text-brand-400 text-sm font-semibold uppercase tracking-wide mb-3 motion-safe:animate-[fade-in-up_0.6s_ease-out_0.1s_both]"
                >
                    {{ eyebrow }}
                </p>
                <h1 class="text-4xl font-bold leading-tight mb-4 motion-safe:animate-[fade-in-up_0.6s_ease-out_0.2s_both]">
                    {{ title }}
                </h1>
                <p class="text-gray-300 leading-relaxed motion-safe:animate-[fade-in-up_0.6s_ease-out_0.3s_both]">
                    {{ description }}
                </p>

                <Link
                    v-if="panelLinkHref"
                    :href="panelLinkHref"
                    class="mt-10 inline-flex items-center gap-2 text-sm font-medium border border-white/25 rounded-md px-5 py-2.5 w-fit transition-all duration-300 hover:bg-white hover:text-charcoal motion-safe:animate-[fade-in-up_0.6s_ease-out_0.4s_both]"
                >
                    {{ panelLinkText }}
                    <span aria-hidden="true" class="transition-transform duration-300 group-hover:translate-x-1">&rarr;</span>
                </Link>
            </div>
        </div>
    </div>
</template>

<style scoped>
.auth-form-enter-active {
    transition: opacity 0.5s ease-out, transform 0.5s ease-out;
}
.auth-form-enter-from {
    opacity: 0;
    transform: translateY(16px);
}

@media (prefers-reduced-motion: reduce) {
    .auth-form-enter-active {
        transition: none;
    }
}
</style>
