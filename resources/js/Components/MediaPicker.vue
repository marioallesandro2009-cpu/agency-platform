<script setup>
import { ref } from "vue";
import { useEscapeClose } from "@/composables/useEscapeClose";
import { useFocusTrap } from "@/composables/useFocusTrap";

const props = defineProps({
    modelValue: { type: String, default: "" },
});

const emit = defineEmits(["update:modelValue"]);

const showModal = ref(false);
const media = ref([]);
const loading = ref(false);
const uploading = ref(false);
const error = ref("");
const fileInput = ref(null);
const modalRef = ref(null);
const urlInput = ref("");
const addingFromUrl = ref(false);
const urlError = ref("");

useEscapeClose(() => (showModal.value = false));
useFocusTrap(showModal, modalRef);

async function openModal() {
    showModal.value = true;
    error.value = "";
    loading.value = true;
    try {
        const res = await window.axios.get(route("admin.media.index"), {
            headers: { Accept: "application/json" },
        });
        media.value = res.data;
    } catch (e) {
        error.value = "Gagal memuat media library.";
    } finally {
        loading.value = false;
    }
}

function select(item) {
    emit("update:modelValue", item.url);
    showModal.value = false;
}

function clear() {
    emit("update:modelValue", "");
}

async function handleUpload(event) {
    const file = event.target.files?.[0];
    if (!file) return;

    uploading.value = true;
    error.value = "";

    const formData = new FormData();
    formData.append("file", file);

    try {
        const res = await window.axios.post(route("admin.media.store"), formData, {
            headers: { Accept: "application/json", "Content-Type": "multipart/form-data" },
        });
        media.value.unshift(res.data);
        select(res.data);
    } catch (e) {
        error.value = e.response?.data?.errors?.file?.[0] || "Gagal mengunggah gambar.";
    } finally {
        uploading.value = false;
        if (fileInput.value) fileInput.value.value = "";
    }
}

async function addFromUrl() {
    if (!urlInput.value) return;

    addingFromUrl.value = true;
    urlError.value = "";

    try {
        const res = await window.axios.post(
            route("admin.media.store-from-url"),
            { url: urlInput.value },
            { headers: { Accept: "application/json" } }
        );
        media.value.unshift(res.data);
        select(res.data);
        urlInput.value = "";
    } catch (e) {
        urlError.value = e.response?.data?.errors?.url?.[0] || "Gagal menambahkan gambar dari link.";
    } finally {
        addingFromUrl.value = false;
    }
}
</script>

<template>
    <div>
        <div class="flex items-center gap-3">
            <div class="w-16 h-16 rounded-md border border-gray-200 bg-gray-50 flex-shrink-0 overflow-hidden flex items-center justify-center">
                <img v-if="modelValue" :src="modelValue" class="w-full h-full object-cover" alt="" />
                <span v-else class="text-xs text-gray-300">Kosong</span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-xs text-gray-500 truncate">{{ modelValue || "Belum ada gambar dipilih" }}</p>
                <div class="flex gap-2 mt-1">
                    <button type="button" @click="openModal" class="text-xs text-brand-600 hover:underline">Pilih Gambar</button>
                    <button v-if="modelValue" type="button" @click="clear" class="text-xs text-red-600 hover:underline">Hapus</button>
                </div>
            </div>
        </div>

        <div v-if="showModal" class="fixed inset-0 bg-black/40 flex items-center justify-center z-[60] p-4" @click.self="showModal = false">
            <div ref="modalRef" role="dialog" aria-modal="true" aria-labelledby="media-picker-title" class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[80vh] flex flex-col">
                <div class="flex items-center justify-between px-5 py-3 border-b">
                    <h3 id="media-picker-title" class="font-semibold text-gray-900">Pilih Gambar</h3>
                    <button type="button" @click="showModal = false" aria-label="Tutup" class="text-gray-400 hover:text-gray-600">x</button>
                </div>

                <div class="px-5 py-3 border-b space-y-2">
                    <label class="inline-flex items-center gap-2 text-sm bg-brand-600 text-white px-3 py-1.5 rounded-md cursor-pointer hover:bg-brand-700">
                        {{ uploading ? "Mengunggah..." : "Unggah Gambar Baru" }}
                        <input ref="fileInput" type="file" accept="image/*" class="hidden" :disabled="uploading" @change="handleUpload" />
                    </label>
                    <p v-if="error" class="text-xs text-red-500">{{ error }}</p>

                    <div class="flex gap-2">
                        <input
                            v-model="urlInput"
                            type="url"
                            placeholder="Atau tempel link gambar (Unsplash/Pexels/Pixabay...)"
                            class="flex-1 border border-gray-300 rounded-md px-3 py-1.5 text-xs focus:ring-2 focus:ring-brand-500 focus:outline-none"
                            @keyup.enter="addFromUrl"
                        />
                        <button
                            type="button"
                            :disabled="addingFromUrl || !urlInput"
                            @click="addFromUrl"
                            class="text-xs font-medium border border-gray-300 px-3 py-1.5 rounded-md hover:bg-gray-50 disabled:opacity-50"
                        >
                            {{ addingFromUrl ? "Memeriksa..." : "Tambah dari Link" }}
                        </button>
                    </div>
                    <p v-if="urlError" class="text-xs text-red-500">{{ urlError }}</p>
                    <p class="text-[11px] text-gray-400">Hanya link dari sumber stock foto gratis-komersial (Unsplash, Pexels, Pixabay, Burst, StockSnap) yang diterima.</p>
                </div>

                <div class="p-5 overflow-auto flex-1">
                    <p v-if="loading" class="text-sm text-gray-400 text-center py-8">Memuat...</p>
                    <p v-else-if="!media.length" class="text-sm text-gray-400 text-center py-8">Belum ada gambar di media library.</p>
                    <div v-else class="grid grid-cols-4 gap-3">
                        <button
                            v-for="item in media"
                            :key="item.id"
                            type="button"
                            @click="select(item)"
                            class="aspect-square rounded-md border border-gray-200 overflow-hidden hover:ring-2 hover:ring-brand-500"
                            :title="item.name"
                        >
                            <img :src="item.url" class="w-full h-full object-cover" :alt="item.name" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
