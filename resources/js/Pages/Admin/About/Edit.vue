<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import MediaPicker from "@/Components/MediaPicker.vue";
import { ICON_OPTIONS } from "@/constants/icons";
import { useForm, usePage } from "@inertiajs/vue3";

const props = defineProps({
    section: Object,
});

const page = usePage();

const form = useForm({
    title: props.section.title || "",
    subtitle: props.section.subtitle || "",
    image: props.section.image || "",
    content: {
        description: props.section.content?.description || "",
        mission: props.section.content?.mission || "",
        mission_icon: props.section.content?.mission_icon || "",
        vision: props.section.content?.vision || "",
        vision_icon: props.section.content?.vision_icon || "",
        values: (props.section.content?.values || []).map((v) => ({ title: v.title || "", description: v.description || "", icon: v.icon || "" })),
        stats: (props.section.content?.stats || []).map((s) => ({ key: s.key, label: s.label || "", is_visible: s.is_visible ?? true })),
    },
});

const STAT_KEY_HINT = {
    portfolio: "Dihitung otomatis dari jumlah proyek Portfolio yang published.",
    blog: "Dihitung otomatis dari jumlah artikel Blog yang published.",
    techstack: "Dihitung otomatis dari jumlah Tech Stack yang aktif.",
    rating: "Dihitung otomatis dari rata-rata rating Testimonial published.",
};

function addCard() {
    form.content.values.push({ title: "", description: "", icon: "" });
}

function removeCard(index) {
    form.content.values.splice(index, 1);
}

function submit() {
    form.put(route("admin.about.update"), {
        preserveScroll: true,
    });
}
</script>

<template>
    <AdminLayout>
        <template #header>Tentang Kami</template>

        <div v-if="page.props.flash?.success" class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded">
            {{ page.props.flash.success }}
        </div>

        <form @submit.prevent="submit" class="space-y-6 max-w-3xl">
            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <h2 class="font-semibold text-gray-900">Header Halaman</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Eyebrow / Subjudul Kecil</label>
                        <input v-model="form.subtitle" type="text" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="Siapa Kami" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Judul</label>
                        <input v-model="form.title" type="text" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="Tentang Kami" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <div>
                    <h2 class="font-semibold text-gray-900">Deskripsi</h2>
                    <p class="text-xs text-gray-500 mb-2">Paragraf pembuka yang tampil rata kiri di bawah header halaman.</p>
                    <textarea
                        v-model="form.content.description"
                        rows="4"
                        class="w-full border rounded-md px-3 py-2 text-sm"
                        placeholder="Kami adalah agensi pengembangan website yang..."
                    ></textarea>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-900 mb-1">Gambar di Samping Deskripsi</h2>
                    <p class="text-xs text-gray-500 mb-2">Tampil di kolom kanan, sejajar dengan paragraf. Kalau kosong, placeholder abu-abu akan ditampilkan sementara.</p>
                    <MediaPicker v-model="form.image" />
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h2 class="font-semibold text-gray-900 mb-2">Misi</h2>
                    <textarea v-model="form.content.mission" rows="3" class="w-full border rounded-md px-3 py-2 text-sm mb-2"></textarea>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Icon</label>
                    <select v-model="form.content.mission_icon" class="w-full border rounded-md px-3 py-2 text-sm">
                        <option value="">- Default (Target) -</option>
                        <option v-for="opt in ICON_OPTIONS" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                    </select>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-900 mb-2">Visi</h2>
                    <textarea v-model="form.content.vision" rows="3" class="w-full border rounded-md px-3 py-2 text-sm mb-2"></textarea>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Icon</label>
                    <select v-model="form.content.vision_icon" class="w-full border rounded-md px-3 py-2 text-sm">
                        <option value="">- Default (Compass) -</option>
                        <option v-for="opt in ICON_OPTIONS" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                    </select>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-gray-900">Nilai-Nilai Kami (Kartu)</h2>
                        <p class="text-xs text-gray-500">Kartu yang tampil di section "Nilai-Nilai Kami". Tambah sebanyak yang dibutuhkan.</p>
                    </div>
                    <button type="button" @click="addCard" class="text-sm bg-brand-600 text-white px-3 py-1.5 rounded-md hover:bg-brand-700">
                        + Tambah Kartu
                    </button>
                </div>

                <p v-if="!form.content.values.length" class="text-sm text-gray-400 text-center py-6 border border-dashed rounded-md">
                    Belum ada kartu. Klik "Tambah Kartu" untuk menambahkan.
                </p>

                <div v-for="(card, index) in form.content.values" :key="index" class="border border-gray-200 rounded-md p-4 relative">
                    <button
                        type="button"
                        @click="removeCard(index)"
                        class="absolute top-3 right-3 text-xs text-red-600 hover:underline"
                    >
                        Hapus
                    </button>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pr-16">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Judul Kartu</label>
                            <input v-model="card.title" type="text" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="mis. Profesional" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Icon</label>
                            <select v-model="card.icon" class="w-full border rounded-md px-3 py-2 text-sm">
                                <option value="">- Pilih icon -</option>
                                <option v-for="opt in ICON_OPTIONS" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi Kartu</label>
                        <input v-model="card.description" type="text" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="Penjelasan singkat" />
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 space-y-4">
                <div>
                    <h2 class="font-semibold text-gray-900">Statistik</h2>
                    <p class="text-xs text-gray-500">
                        Tampil di halaman Tentang Kami sebagai angka pencapaian. Angkanya dihitung otomatis dari data asli —
                        Anda cuma bisa mengubah label atau menyembunyikannya, tidak bisa mengisi angka manual.
                    </p>
                </div>
                <div v-for="stat in form.content.stats" :key="stat.key" class="flex items-start gap-4 border border-gray-200 rounded-md p-3">
                    <label class="flex items-center gap-2 pt-2 shrink-0">
                        <input v-model="stat.is_visible" type="checkbox" />
                        <span class="text-xs font-medium text-gray-600">Tampilkan</span>
                    </label>
                    <div class="flex-1 min-w-0">
                        <input v-model="stat.label" type="text" class="w-full border rounded-md px-3 py-2 text-sm mb-1" />
                        <p class="text-xs text-gray-400">{{ STAT_KEY_HINT[stat.key] }}</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button
                    type="submit"
                    :disabled="form.processing"
                    class="bg-brand-600 text-white text-sm font-medium px-6 py-2.5 rounded-md hover:bg-brand-700 disabled:opacity-50"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </AdminLayout>
</template>
