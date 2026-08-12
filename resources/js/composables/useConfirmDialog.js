import { reactive } from "vue";

const state = reactive({
    visible: false,
    title: "",
    message: "",
    confirmLabel: "Konfirmasi",
    cancelLabel: "Batal",
    danger: false,
    resolve: null,
});

export function useConfirmDialogState() {
    return state;
}

export function confirmDialog(options) {
    const { title = "", message = "", confirmLabel = "Konfirmasi", cancelLabel = "Batal", danger = false } =
        typeof options === "string" ? { message: options } : options;

    return new Promise((resolve) => {
        state.title = title;
        state.message = message;
        state.confirmLabel = confirmLabel;
        state.cancelLabel = cancelLabel;
        state.danger = danger;
        state.resolve = resolve;
        state.visible = true;
    });
}
