<script setup>
defineProps({
    message: { type: String, default: "Belum ada data." },
});
</script>

<template>
    <div class="px-5 py-12 text-center">
        <svg class="w-10 h-10 mx-auto text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M20 13V7a2 2 0 00-2-2H6a2 2 0 00-2 2v6m16 0l-2.5 6H6.5L4 13m16 0H4m5 3h6" />
        </svg>
        <p class="text-sm text-gray-400">{{ message }}</p>
        <div v-if="$slots.action" class="mt-4">
            <slot name="action" />
        </div>
    </div>
</template>
