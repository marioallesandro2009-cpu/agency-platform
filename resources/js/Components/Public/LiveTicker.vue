<script setup>
/**
 * Slim social-proof bar fed by real (masked) lead activity — see
 * Lead::recentTickerActivity(). Renders nothing if the backend didn't
 * supply enough real entries (no dummy/placeholder data, ever) or if the
 * visitor already dismissed it this session.
 */
import { computed, onMounted, onUnmounted, ref } from "vue";

const props = defineProps({
    activities: { type: Array, default: () => [] },
});

const DISMISS_KEY = "liveTickerDismissed";
const ROTATE_MS = 4500;

const dismissed = ref(true); // starts hidden until the sessionStorage check on mount, to avoid a flash
const currentIndex = ref(0);
let timer = null;

const current = computed(() => props.activities[currentIndex.value] || null);

function dismiss() {
    dismissed.value = true;
    sessionStorage.setItem(DISMISS_KEY, "1");
    if (timer) clearInterval(timer);
}

onMounted(() => {
    if (!props.activities.length) return;
    dismissed.value = sessionStorage.getItem(DISMISS_KEY) === "1";
    if (dismissed.value) return;

    timer = setInterval(() => {
        currentIndex.value = (currentIndex.value + 1) % props.activities.length;
    }, ROTATE_MS);
});

onUnmounted(() => {
    if (timer) clearInterval(timer);
});
</script>

<template>
    <div v-if="!dismissed && current" class="bg-brand-600 text-white text-sm">
        <div class="max-w-7xl mx-auto pl-4 pr-10 py-2 flex items-center justify-center gap-3 relative">
            <Transition name="ticker-fade" mode="out-in">
                <p :key="currentIndex" class="min-w-0 flex-1 text-center truncate">
                    <span class="font-semibold">{{ current.name }}</span> baru saja memesan {{ current.service }}
                </p>
            </Transition>
            <button
                @click="dismiss"
                aria-label="Tutup notifikasi"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-white/70 hover:text-white text-base leading-none"
            >
                &times;
            </button>
        </div>
    </div>
</template>

<style scoped>
.ticker-fade-enter-active,
.ticker-fade-leave-active {
    transition: opacity 0.4s ease, transform 0.4s ease;
}
.ticker-fade-enter-from {
    opacity: 0;
    transform: translateY(4px);
}
.ticker-fade-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}
</style>
