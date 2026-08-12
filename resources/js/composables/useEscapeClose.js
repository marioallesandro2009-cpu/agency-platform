import { onMounted, onUnmounted } from "vue";

export function useEscapeClose(closeCallback) {
    function handleKeydown(event) {
        if (event.key === "Escape") closeCallback();
    }

    onMounted(() => document.addEventListener("keydown", handleKeydown));
    onUnmounted(() => document.removeEventListener("keydown", handleKeydown));
}
