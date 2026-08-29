<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import EmptyState from "@/Components/Admin/EmptyState.vue";
import MediaPicker from "@/Components/MediaPicker.vue";
import RichTextEditor from "@/Components/Admin/RichTextEditor.vue";
import { router, usePage } from "@inertiajs/vue3";
import { reactive, ref } from "vue";
import { useEscapeClose } from "@/composables/useEscapeClose";
import { useFocusTrap } from "@/composables/useFocusTrap";
import { confirmDialog } from "@/composables/useConfirmDialog";
import { ICON_OPTIONS } from "@/constants/icons";

const props = defineProps({
    services: Array,
    blogCategories: Array,
});

const page = usePage();
const showForm = ref(false);
const editingId = ref(null);
const modalRef = ref(null);

useEscapeClose(() => (showForm.value = false));
useFocusTrap(showForm, modalRef);

const form = reactive({
    name: "",
    short_description: "",
    badge_label: "",
    long_description: "",
    icon: "",
    image: "",
    features: "",
    benefits: "",
    target_audience: "",
    process_steps: "",
    faqs_raw: "",
    cta_text: "Konsultasi Gratis Sekarang",
    cta_url: "/konsultasi",
    blog_category_id: "",
    show_portfolio: true,
    is_featured: false,
    order: 0,
    seo_title: "",
    seo_description: "",
});

function resetForm() {
    Object.assign(form, {
        name: "", short_description: "", badge_label: "", long_description: "", icon: "", image: "", features: "", benefits: "",
        target_audience: "", process_steps: "", faqs_raw: "",
        cta_text: "Konsultasi Gratis Sekarang", cta_url: "/konsultasi", blog_category_id: "",
        show_portfolio: true, is_featured: false, order: 0, seo_title: "", seo_description: "",
    });
}

function openCreate() {
    editingId.value = null;
    resetForm();
    showForm.value = true;
}

function openEdit(s) {
    editingId.value = s.id;
    Object.assign(form, {
        name: s.name,
        short_description: s.short_description || "",
        badge_label: s.badge_label || "",
        long_description: s.long_description || "",
        icon: s.icon || "",
        image: s.image || "",
        features: (s.features || []).join(", "),
        benefits: (s.benefits || []).join(", "),
        target_audience: s.target_audience || "",
        process_steps: (s.process_steps || []).join(", "),
        faqs_raw: (s.faqs || []).map((f) => `${f.question} :: ${f.answer}`).join("\n"),
        cta_text: s.cta_text || "",
        cta_url: s.cta_url || "",
        blog_category_id: s.blog_category_id || "",
        show_portfolio: s.show_portfolio,
        is_featured: s.is_featured,
        order: s.order,
        seo_title: s.seo_title || "",
        seo_description: s.seo_description || "",
    });
    showForm.value = true;
}

function submit() {
    const payload = {
        ...form,
        features: form.features.split(",").map((f) => f.trim()).filter(Boolean),
        benefits: form.benefits.split(",").map((f) => f.trim()).filter(Boolean),
        process_steps: form.process_steps.split(",").map((f) => f.trim()).filter(Boolean),
    };

    if (editingId.value) {
        router.put(route("admin.services.update", editingId.value), payload, { onSuccess: () => (showForm.value = false) });
    } else {
        router.post(route("admin.services.store"), payload, { onSuccess: () => (showForm.value = false) });
    }
}

function togglePublish(s) {
    router.patch(route("admin.services.toggle-publish", s.id), {}, { preserveScroll: true });
}

async function destroyService(s) {
    if (await confirmDialog({ message: `Hapus layanan "${s.name}"?`, confirmLabel: "Hapus", danger: true })) {
        router.delete(route("admin.services.destroy", s.id), { preserveScroll: true });
    }
}
</script>

<template>
    <AdminLayout>
        <template #header>Layanan (Jasa)</template>

        <div v-if="page.props.flash?.success" class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded">
            {{ page.props.flash.success }}
        </div>

        <button @click="openCreate" class="mb-4 bg-brand-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-brand-700">
            + Tambah Layanan
        </button>

        <div class="bg-white rounded-lg shadow divide-y">
            <EmptyState v-if="!services.length" message="Belum ada layanan." />
            <div v-for="s in services" :key="s.id" class="flex items-center justify-between px-5 py-4">
                <div>
                    <p class="font-medium text-gray-900">{{ s.name }}</p>
                    <p class="text-sm text-gray-500">{{ s.short_description }}</p>
                    <p class="text-xs text-gray-400 mt-1">
                        Slug: /{{ s.slug }}
                        <span v-if="s.blog_category"> - Kategori blog: {{ s.blog_category.name }}</span>
                        <span v-if="!s.show_portfolio"> - Portfolio disembunyikan</span>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        @click="togglePublish(s)"
                        :class="s.is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                        class="text-xs font-medium px-3 py-1 rounded-full"
                    >
                        {{ s.is_published ? "Published" : "Draft" }}
                    </button>
                    <button @click="openEdit(s)" class="text-sm text-brand-600 hover:underline">Edit</button>
                    <button @click="destroyService(s)" class="text-sm text-red-600 hover:underline">Hapus</button>
                </div>
            </div>
        </div>

        <div v-if="showForm" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="showForm = false">
            <div ref="modalRef" role="dialog" aria-modal="true" aria-labelledby="service-form-title" class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-auto">
                <h2 id="service-form-title" class="text-lg font-semibold mb-4">{{ editingId ? "Edit" : "Tambah" }} Layanan</h2>
                <form @submit.prevent="submit" class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama Layanan</label>
                        <input v-model="form.name" type="text" required class="w-full border rounded-md px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Deskripsi Singkat</label>
                        <textarea v-model="form.short_description" class="w-full border rounded-md px-3 py-2 text-sm" rows="2"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Badge (opsional, tampil di atas nomor urut homepage)</label>
                        <input v-model="form.badge_label" type="text" placeholder="mis. Paling Populer" class="w-full border rounded-md px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Konten Lengkap (ditampilkan gaya blog)</label>
                        <RichTextEditor v-model="form.long_description" />
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium mb-1">Icon (kartu di homepage)</label>
                            <select v-model="form.icon" class="w-full border rounded-md px-3 py-2 text-sm">
                                <option value="">- Pilih icon -</option>
                                <option v-for="opt in ICON_OPTIONS" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Gambar (kartu di homepage)</label>
                            <MediaPicker v-model="form.image" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Fitur (pisahkan koma)</label>
                        <input v-model="form.features" type="text" class="w-full border rounded-md px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Manfaat (pisahkan koma)</label>
                        <input v-model="form.benefits" type="text" class="w-full border rounded-md px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Target Audiens</label>
                        <textarea v-model="form.target_audience" placeholder="mis. Pelaku UMKM dan usaha kecil yang baru mulai membangun kehadiran online" class="w-full border rounded-md px-3 py-2 text-sm" rows="2"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Proses Pengerjaan (pisahkan koma, berurutan)</label>
                        <input v-model="form.process_steps" type="text" placeholder="Konsultasi, Desain, Development, Testing, Launching" class="w-full border rounded-md px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">FAQ (satu baris per pertanyaan, format: Pertanyaan :: Jawaban)</label>
                        <textarea v-model="form.faqs_raw" placeholder="Berapa lama pengerjaan? :: Umumnya 2-4 minggu tergantung kompleksitas." class="w-full border rounded-md px-3 py-2 text-sm font-mono text-xs" rows="4"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium mb-1">CTA Text</label>
                            <input v-model="form.cta_text" type="text" class="w-full border rounded-md px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">CTA URL</label>
                            <input v-model="form.cta_url" type="text" class="w-full border rounded-md px-3 py-2 text-sm" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Kategori Blog Terkait (buat sidebar "Terkait")</label>
                        <select v-model="form.blog_category_id" class="w-full border rounded-md px-3 py-2 text-sm">
                            <option value="">- Tidak ada -</option>
                            <option v-for="cat in blogCategories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-6">
                        <label class="flex items-center gap-2 text-sm">
                            <input v-model="form.show_portfolio" type="checkbox" />
                            Tampilkan Portfolio Terkait
                        </label>
                        <label class="flex items-center gap-2 text-sm">
                            <input v-model="form.is_featured" type="checkbox" />
                            Featured
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Urutan</label>
                        <input v-model="form.order" type="number" class="w-full border rounded-md px-3 py-2 text-sm" />
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showForm = false" class="px-4 py-2 text-sm text-gray-600">Batal</button>
                        <button type="submit" class="bg-brand-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-brand-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>