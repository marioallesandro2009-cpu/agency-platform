import { nextTick, watch } from "vue";

const FOCUSABLE_SELECTOR = 'a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])';

/**
 * Keeps Tab/Shift+Tab cycling inside the modal while it's open, moves focus
 * into it on open, and restores focus to whatever triggered it on close —
 * without this, keyboard users can tab straight through to the page behind
 * a modal overlay, or lose their place entirely once it closes.
 */
export function useFocusTrap(isOpenRef, containerRef) {
    let previouslyFocused = null;

    function getFocusable() {
        if (!containerRef.value) return [];
        return Array.from(containerRef.value.querySelectorAll(FOCUSABLE_SELECTOR));
    }

    function handleKeydown(event) {
        if (event.key !== "Tab") return;

        const focusable = getFocusable();
        if (!focusable.length) return;

        const first = focusable[0];
        const last = focusable[focusable.length - 1];

        if (event.shiftKey && document.activeElement === first) {
            event.preventDefault();
            last.focus();
        } else if (!event.shiftKey && document.activeElement === last) {
            event.preventDefault();
            first.focus();
        }
    }

    watch(isOpenRef, async (isOpen) => {
        if (isOpen) {
            previouslyFocused = document.activeElement;
            await nextTick();
            const focusable = getFocusable();
            (focusable[0] || containerRef.value)?.focus();
            document.addEventListener("keydown", handleKeydown);
        } else {
            document.removeEventListener("keydown", handleKeydown);
            previouslyFocused?.focus?.();
            previouslyFocused = null;
        }
    });
}
