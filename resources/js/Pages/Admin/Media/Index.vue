<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import EmptyState from "@/Components/Admin/EmptyState.vue";
import { router, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import { confirmDialog } from "@/composables/useConfirmDialog";

defineProps({
    media: Array,
});

const page = usePage();
const uploading = ref(false);
const error = ref("");
const urlInput = ref("");
const addingFromUrl = ref(false);
const urlError = ref("");

function formatSize(bytes) {
    if (bytes < 1024) return bytes + " B";
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + " KB";
    return (bytes / (1024 * 1024)).toFixed(1) + " MB";
}

async function handleUpload(event) {
    const file = event.target.files?.[0];
    if (!file) return;

    uploading.value = true;
    error.value = "";

    const formData = new FormData();
    formData.append("file", file);

    router.post(route("admin.media.store"), formData, {
        forceFormData: true,
        onError: (errors) => (error.value = errors.file || "Gagal mengunggah gambar."),
        onFinish: () => {
            uploading.value = false;
            event.target.value = "";
        },
    });
}

function addFromUrl() {
    if (!urlInput.value) return;

    addingFromUrl.value = true;
    urlError.value = "";

    router.post(
        route("admin.media.store-from-url"),
        { url: urlInput.value },
        {
            preserveScroll: true,
            onSuccess: () => (urlInput.value = ""),
            onError: (errors) => (urlError.value = errors.url || "Gagal menambahkan gambar dari link."),
            onFinish: () => (addingFromUrl.value = false),
        }
    );
}

async function destroyMedia(item) {
    if (await confirmDialog({ message: `Hapus gambar "${item.name}"?`, confirmLabel: "Hapus", danger: true })) {
        router.delete(route("admin.media.destroy", item.id), { preserveScroll: true });
    }
}

function copyUrl(item) {
    navigator.clipboard?.writeText(item.url);
}
</script>

<template>
    <AdminLayout>
        <template #header>Media Library</template>

        <div v-if="page.props.flash?.success" class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded">
            {{ page.props.flash.success }}
        </div>

        <div class="bg-white rounded-lg shadow p-4 mb-6 space-y-3">
            <label class="inline-flex items-center gap-2 text-sm bg-brand-600 text-white px-4 py-2 rounded-md cursor-pointer hover:bg-brand-700">
                {{ uploading ? "Mengunggah..." : "+ Unggah Gambar" }}
                <input type="file" accept="image/*" class="hidden" :disabled="uploading" @change="handleUpload" />
            </label>
            <p v-if="error" class="text-xs text-red-500">{{ error }}</p>

            <div class="flex gap-2 max-w-lg">
                <input
                    v-model="urlInput"
                    type="url"
                    placeholder="Atau tempel link gambar (Unsplash/Pexels/Pixabay...)"
                    class="flex-1 border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none"
                    @keyup.enter="addFromUrl"
                />
                <button
                    type="button"
                    :disabled="addingFromUrl || !urlInput"
                    @click="addFromUrl"
                    class="text-sm font-medium border border-gray-300 px-4 py-2 rounded-md hover:bg-gray-50 disabled:opacity-50 whitespace-nowrap"
                >
                    {{ addingFromUrl ? "Memeriksa..." : "Tambah dari Link" }}
                </button>
            </div>
            <p v-if="urlError" class="text-xs text-red-500">{{ urlError }}</p>
            <p class="text-xs text-gray-400">Hanya link dari sumber stock foto gratis-komersial (Unsplash, Pexels, Pixabay, Burst, StockSnap) yang diterima — sumber lain otomatis ditolak.</p>
        </div>

        <div v-if="!media.length" class="bg-white rounded-lg shadow">
            <EmptyState message="Belum ada gambar diunggah." />
        </div>
        <div v-else class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-4">
            <div v-for="item in media" :key="item.id" class="bg-white rounded-lg shadow overflow-hidden">
                <div class="aspect-square bg-gray-50">
                    <img :src="item.url" class="w-full h-full object-cover" :alt="item.name" />
                </div>
                <div class="p-2">
                    <p class="text-xs text-gray-700 truncate" :title="item.name">{{ item.name }}</p>
                    <p class="text-xs text-gray-400">{{ formatSize(item.size) }}</p>
                    <div class="flex gap-2 mt-1">
                        <button @click="copyUrl(item)" class="text-xs text-brand-600 hover:underline">Salin URL</button>
                        <button @click="destroyMedia(item)" class="text-xs text-red-600 hover:underline">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
