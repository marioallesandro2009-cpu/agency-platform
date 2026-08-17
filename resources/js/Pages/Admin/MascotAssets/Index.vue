<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import { router, usePage } from "@inertiajs/vue3";
import { reactive } from "vue";

const props = defineProps({
    assets: Array,
});

const page = usePage();

// Human-readable label/description per placement_key, purely for the admin
// UI — the key itself is what the frontend actually matches on.
const META = {
    hero_tentang_kami: {
        label: "Hero Tentang Kami",
        description: "Fallback foto profil di halaman Tentang Kami, dipakai hanya kalau foto profil belum diisi di menu \"Tentang Kami\".",
    },
    whatsapp_widget: {
        label: "Widget WhatsApp",
        description: "Avatar tombol WhatsApp mengambang yang tampil di semua halaman publik.",
    },
    service_sidebar: {
        label: "Sidebar Halaman Jasa",
        description: "Gambar tambahan di sidebar kanan-bawah halaman /jasa-website-*.",
    },
    cta_kontak: {
        label: "CTA Kontak (Homepage)",
        description: "Pengganti ilustrasi dekoratif di section \"Siap Membangun Website Impian Anda?\".",
    },
    kenalan_dekat_kami: {
        label: "Kenalan Lebih Dekat (Homepage)",
        description: "Pengisi ruang kosong di bawah card teks section \"Kenalan Lebih Dekat dengan Kami\".",
    },
    halaman_404: {
        label: "Halaman 404",
        description: "Ilustrasi opsional di halaman \"Halaman Tidak Ditemukan\".",
    },
    empty_state_blog: {
        label: "Blog Kategori Kosong",
        description: "Ditampilkan saat kategori blog belum punya artikel sama sekali.",
    },
    kontak_konfirmasi: {
        label: "Konfirmasi Kontak",
        description: "Ditampilkan pada notifikasi sukses setelah form kontak/CTA berhasil dikirim.",
    },
    intro_tentang_kami: {
        label: "Intro Tentang Kami",
        description: "Pengisi ruang bagian atas section profil, sebelum paragraf deskripsi.",
    },
};

const rows = reactive(
    props.assets.map((asset) => ({
        id: asset.id,
        placement_key: asset.placement_key,
        image_path: asset.image_path,
        caption: asset.caption || "",
        alt_text: asset.alt_text || "",
        file: null,
        previewUrl: null,
        saving: false,
    }))
);

function pickFile(row, event) {
    const file = event.target.files?.[0];
    if (!file) return;
    row.file = file;
    row.previewUrl = URL.createObjectURL(file);
}

function save(row) {
    row.saving = true;

    const formData = new FormData();
    if (row.file) formData.append("image", row.file);
    formData.append("caption", row.caption || "");
    formData.append("alt_text", row.alt_text || "");

    router.post(route("admin.mascot.update", row.id), formData, {
        preserveScroll: true,
        onSuccess: () => {
            row.file = null;
            row.previewUrl = null;
        },
        onFinish: () => {
            row.saving = false;
        },
    });
}
</script>

<template>
    <AdminLayout>
        <template #header>Maskot</template>

        <div v-if="page.props.flash?.success" class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded">
            {{ page.props.flash.success }}
        </div>

        <p class="text-sm text-gray-500 mb-6">
            9 titik ilustrasi/maskot di seluruh situs. Selama gambar belum diisi di sini, halaman publik tampil persis seperti sekarang.
        </p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div v-for="row in rows" :key="row.id" class="bg-white rounded-lg shadow p-5">
                <h2 class="font-semibold text-gray-900">{{ META[row.placement_key]?.label || row.placement_key }}</h2>
                <p class="text-xs text-gray-500 mt-1 mb-4">{{ META[row.placement_key]?.description }}</p>

                <div class="flex gap-4">
                    <div class="w-24 h-24 rounded-md border border-gray-200 bg-gray-50 flex-shrink-0 overflow-hidden flex items-center justify-center">
                        <img v-if="row.previewUrl || row.image_path" :src="row.previewUrl || row.image_path" class="w-full h-full object-cover" alt="" />
                        <span v-else class="text-xs text-gray-300 text-center px-2">Belum ada gambar</span>
                    </div>

                    <div class="flex-1 min-w-0 space-y-2">
                        <label class="inline-flex items-center gap-2 text-xs bg-brand-600 text-white px-3 py-1.5 rounded-md cursor-pointer hover:bg-brand-700">
                            Unggah / Ganti Gambar
                            <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="pickFile(row, $event)" />
                        </label>
                        <p class="text-[11px] text-gray-400">JPG/PNG/WEBP, maks 2MB.</p>

                        <input
                            v-model="row.caption"
                            type="text"
                            placeholder="Caption (opsional)"
                            class="w-full border rounded-md px-2 py-1.5 text-xs"
                        />
                        <input
                            v-model="row.alt_text"
                            type="text"
                            placeholder="Alt text (opsional, untuk aksesibilitas)"
                            class="w-full border rounded-md px-2 py-1.5 text-xs"
                        />
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <button
                        type="button"
                        :disabled="row.saving"
                        @click="save(row)"
                        class="bg-brand-600 text-white text-xs font-medium px-4 py-2 rounded-md hover:bg-brand-700 disabled:opacity-50"
                    >
                        {{ row.saving ? "Menyimpan..." : "Simpan" }}
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
