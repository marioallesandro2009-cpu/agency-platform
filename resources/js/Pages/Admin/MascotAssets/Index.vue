<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import { router, usePage } from "@inertiajs/vue3";
import { reactive, watch } from "vue";

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
    sidebar_jasa_umkm: {
        label: "Sidebar Jasa — UMKM",
        description: "Gambar tambahan di sidebar kanan-bawah halaman /jasa-website-umkm.",
    },
    sidebar_jasa_company_profile: {
        label: "Sidebar Jasa — Company Profile",
        description: "Gambar tambahan di sidebar kanan-bawah halaman /jasa-website-company-profile.",
    },
    sidebar_jasa_toko_online: {
        label: "Sidebar Jasa — Toko Online",
        description: "Gambar tambahan di sidebar kanan-bawah halaman /jasa-toko-online.",
    },
    sidebar_jasa_web_development: {
        label: "Sidebar Jasa — Web Development",
        description: "Gambar tambahan di sidebar kanan-bawah halaman /jasa-web-development.",
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
        deleting: false,
        transparencyWarning: false,
    }))
);

// `rows` is a local reactive copy built once from the initial props so the
// caption/alt_text inputs stay editable without fighting Inertia's own
// reactivity. Without this watcher, a fresh `assets` payload from the server
// (after upload or delete) never reaches the already-mounted rows, which is
// why the preview used to require a manual browser refresh to show up.
watch(
    () => props.assets,
    (freshAssets) => {
        freshAssets.forEach((asset) => {
            const row = rows.find((r) => r.id === asset.id);
            if (!row) return;
            row.image_path = asset.image_path;
            row.caption = asset.caption || "";
            row.alt_text = asset.alt_text || "";
        });
    },
    { deep: true }
);

// Reads the picked PNG file into a small offscreen canvas and checks whether
// every sampled pixel is fully opaque (alpha 255) — a strong signal the
// "transparent" PNG is actually flat/opaque and will show a solid background
// box on the public site. Downscaling to 64x64 keeps this cheap regardless
// of the original image size.
function checkPngTransparency(file) {
    return new Promise((resolve) => {
        const img = new Image();
        const url = URL.createObjectURL(file);

        const cleanup = (result) => {
            URL.revokeObjectURL(url);
            resolve(result);
        };

        img.onload = () => {
            try {
                const size = 64;
                const canvas = document.createElement("canvas");
                canvas.width = size;
                canvas.height = size;
                const ctx = canvas.getContext("2d");
                ctx.drawImage(img, 0, 0, size, size);
                const data = ctx.getImageData(0, 0, size, size).data;

                let hasTransparency = false;
                for (let i = 3; i < data.length; i += 4) {
                    if (data[i] < 255) {
                        hasTransparency = true;
                        break;
                    }
                }
                cleanup(hasTransparency);
            } catch (e) {
                // Can't read pixel data (e.g. canvas tainted) — fail open,
                // don't block or falsely warn the user.
                cleanup(true);
            }
        };
        img.onerror = () => cleanup(true);
        img.src = url;
    });
}

function pickFile(row, event) {
    const file = event.target.files?.[0];
    if (!file) return;
    row.file = file;
    row.previewUrl = URL.createObjectURL(file);
    row.transparencyWarning = false;

    if (file.type === "image/png") {
        checkPngTransparency(file).then((hasTransparency) => {
            row.transparencyWarning = !hasTransparency;
        });
    }
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
            row.transparencyWarning = false;
        },
        onFinish: () => {
            row.saving = false;
        },
    });
}

function destroyImage(row) {
    if (!window.confirm("Hapus gambar ini? Halaman publik akan kembali ke tampilan default.")) {
        return;
    }

    row.deleting = true;

    router.delete(route("admin.mascot.destroy", row.id), {
        preserveScroll: true,
        onSuccess: () => {
            row.file = null;
            row.previewUrl = null;
            row.transparencyWarning = false;
        },
        onFinish: () => {
            row.deleting = false;
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
            12 titik ilustrasi/maskot di seluruh situs. Selama gambar belum diisi di sini, halaman publik tampil persis seperti sekarang.
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
                        <div class="flex flex-wrap items-center gap-2">
                            <label class="inline-flex items-center gap-2 text-xs bg-brand-600 text-white px-3 py-1.5 rounded-md cursor-pointer hover:bg-brand-700">
                                Unggah / Ganti Gambar
                                <input type="file" accept="image/jpeg,image/png,image/webp" class="hidden" @change="pickFile(row, $event)" />
                            </label>
                            <button
                                v-if="row.image_path && !row.previewUrl"
                                type="button"
                                :disabled="row.deleting"
                                @click="destroyImage(row)"
                                class="inline-flex items-center gap-2 text-xs bg-red-50 text-red-700 border border-red-200 px-3 py-1.5 rounded-md hover:bg-red-100 disabled:opacity-50"
                            >
                                {{ row.deleting ? "Menghapus..." : "Hapus Gambar" }}
                            </button>
                        </div>
                        <p class="text-[11px] text-gray-400">JPG/PNG/WEBP, maks 2MB.</p>
                        <p v-if="row.transparencyWarning" class="text-[11px] text-amber-800 bg-amber-50 border border-amber-200 rounded-md px-2 py-1.5">
                            <strong>Peringatan:</strong> gambar ini terdeteksi tidak memiliki background transparan. Background solid akan ikut tampil di halaman publik. Yakin ingin lanjut upload?
                        </p>

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
