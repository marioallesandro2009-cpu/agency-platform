<script setup>
/**
 * App-wide confirm dialog: promise-based replacement for window.confirm().
 * Behavior/API (promise-based confirm, custom title/message/buttons) took
 * inspiration from the "confirm-dialog" library by Isaac Hernandez (MIT,
 * 2023) — see THIRD-PARTY-LICENSES.md — but this is a from-scratch Vue 3
 * Composition API implementation, not a port of its DOM-manipulation code.
 *
 * Mount once (see AdminLayout.vue); call confirmDialog({ message, ... })
 * from anywhere to await a boolean result instead of using window.confirm().
 */
import { computed, ref } from "vue";
import { useConfirmDialogState } from "@/composables/useConfirmDialog";
import { useEscapeClose } from "@/composables/useEscapeClose";
import { useFocusTrap } from "@/composables/useFocusTrap";

const state = useConfirmDialogState();
const modalRef = ref(null);
const isOpen = computed(() => state.visible);

useFocusTrap(isOpen, modalRef);

function settle(value) {
    state.visible = false;
    state.resolve?.(value);
    state.resolve = null;
}

useEscapeClose(() => {
    if (state.visible) settle(false);
});
</script>

<template>
    <Teleport to="body">
        <div
            v-if="state.visible"
            class="fixed inset-0 bg-black/40 flex items-center justify-center z-[100] p-4"
            @click.self="settle(false)"
        >
            <div
                ref="modalRef"
                role="alertdialog"
                aria-modal="true"
                aria-labelledby="confirm-dialog-title"
                aria-describedby="confirm-dialog-message"
                class="bg-white rounded-xl shadow-xl max-w-sm w-full p-5 animate-[confirm-pop_0.15s_ease-out]"
            >
                <h3 id="confirm-dialog-title" class="font-semibold text-gray-900 mb-1">
                    {{ state.title || "Konfirmasi" }}
                </h3>
                <p id="confirm-dialog-message" class="text-sm text-gray-600 mb-5">
                    {{ state.message }}
                </p>
                <div class="flex justify-end gap-2">
                    <button
                        type="button"
                        @click="settle(false)"
                        class="text-sm font-medium text-gray-600 px-4 py-2 rounded-md hover:bg-gray-100"
                    >
                        {{ state.cancelLabel }}
                    </button>
                    <button
                        type="button"
                        autofocus
                        @click="settle(true)"
                        class="text-sm font-medium text-white px-4 py-2 rounded-md"
                        :class="state.danger ? 'bg-red-600 hover:bg-red-700' : 'bg-brand-600 hover:bg-brand-700'"
                    >
                        {{ state.confirmLabel }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
@keyframes confirm-pop {
    from {
        opacity: 0;
        transform: scale(0.96) translateY(4px);
    }
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}
</style>
