<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import MediaPicker from "@/Components/MediaPicker.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { useEscapeClose } from "@/composables/useEscapeClose";
import { useFocusTrap } from "@/composables/useFocusTrap";
import { ICON_OPTIONS } from "@/constants/icons";

const props = defineProps({
    sections: Array,
});

const page = usePage();
const localSections = ref([...props.sections]);
const pendingIds = ref(new Set());

const editableTypes = ["about", "why_us", "hero"];
const showForm = ref(false);
const modalRef = ref(null);
const editingSection = ref(null);

useEscapeClose(() => (showForm.value = false));
useFocusTrap(showForm, modalRef);

const form = useForm({
    title: "",
    subtitle: "",
    image: "",
    description: "",
    button_text: "",
    button_url: "",
    items: [],
    eyebrow: "",
    cta_primary_text: "",
    cta_primary_url: "",
    cta_secondary_text: "",
    cta_secondary_url: "",
});

function openEdit(section) {
    editingSection.value = section;
    form.clearErrors();
    form.title = section.title || "";
    form.subtitle = section.subtitle || "";
    form.image = section.image || "";
    form.description = section.content?.description || "";
    form.button_text = section.content?.button_text || "";
    form.button_url = section.content?.button_url || "";
    form.items = (section.content?.items || []).map((i) => ({
        title: i.title || "",
        description: i.description || "",
        icon: i.icon || "",
    }));
    form.eyebrow = section.content?.eyebrow || "";
    form.cta_primary_text = section.content?.cta_primary_text || "";
    form.cta_primary_url = section.content?.cta_primary_url || "";
    form.cta_secondary_text = section.content?.cta_secondary_text || "";
    form.cta_secondary_url = section.content?.cta_secondary_url || "";
    showForm.value = true;
}

function addItem() {
    form.items.push({ title: "", description: "", icon: "" });
}

function removeItem(index) {
    form.items.splice(index, 1);
}

function submit() {
    const type = editingSection.value.section_type;

    const payload = {
        title: form.title,
        image: form.image,
    };

    if (type === "why_us") {
        payload.content = { items: form.items };
    } else if (type === "hero") {
        payload.subtitle = form.subtitle;
        payload.content = {
            eyebrow: form.eyebrow,
            cta_primary_text: form.cta_primary_text,
            cta_primary_url: form.cta_primary_url,
            cta_secondary_text: form.cta_secondary_text,
            cta_secondary_url: form.cta_secondary_url,
        };
    } else {
        payload.content = {
            description: form.description,
            button_text: form.button_text,
            button_url: form.button_url,
        };
    }

    router.patch(route("admin.page-sections.update", editingSection.value.id), payload, {
        preserveScroll: true,
        onSuccess: () => (showForm.value = false),
    });
}

watch(
    () => props.sections,
    (newVal) => {
        localSections.value = [...newVal];
    }
);

function toggle(section) {
    if (pendingIds.value.has(section.id)) return;

    const index = localSections.value.findIndex((s) => s.id === section.id);
    if (index === -1) return;

    const previousValue = localSections.value[index].is_visible;
    localSections.value[index].is_visible = !previousValue;
    pendingIds.value.add(section.id);

    router.patch(
        route("admin.page-sections.toggle", section.id),
        {},
        {
            preserveState: true,
            preserveScroll: true,
            onError: () => {
                localSections.value[index].is_visible = previousValue;
            },
            onFinish: () => {
                pendingIds.value.delete(section.id);
            },
        }
    );
}

const draggedIndex = ref(null);

function onDragStart(index) {
    draggedIndex.value = index;
}

function onDrop(index) {
    if (draggedIndex.value === null || draggedIndex.value === index) return;

    const items = [...localSections.value];
    const [moved] = items.splice(draggedIndex.value, 1);
    items.splice(index, 0, moved);
    localSections.value = items;
    draggedIndex.value = null;

    router.post(
        route("admin.page-sections.reorder"),
        { order: items.map((s) => s.id) },
        { preserveState: true, preserveScroll: true }
    );
}

const sectionLabels = {
    hero: "Hero",
    about: "About (Profil Perusahaan)",
    services: "Layanan (Services)",
    portfolio: "Portfolio",
    why_us: "Why Choose Us",
    process: "Process",
    pricing: "Pricing",
    testimonials: "Testimonials",
    blog: "Blog",
    faq: "FAQ",
    cta: "Final CTA",
};
</script>

<template>
    <AdminLayout>
        <div class="max-w-3xl mx-auto py-8 px-4">
            <h1 class="text-xl font-semibold text-gray-900 mb-1">Homepage Sections</h1>
            <p class="text-sm text-gray-500 mb-6">
                Seret ikon di kiri untuk mengubah urutan. Klik toggle untuk menampilkan/menyembunyikan section.
            </p>

            <div class="bg-white border border-gray-200 rounded-lg divide-y">
                <div
                    v-for="(section, index) in localSections"
                    :key="section.id"
                    @dragover.prevent
                    @drop="onDrop(index)"
                    class="flex items-center justify-between px-4 py-3 hover:bg-gray-50"
                >
                    <div class="flex items-center gap-3">
                        <span
                            draggable="true"
                            @dragstart="onDragStart(index)"
                            class="text-gray-400 cursor-move select-none px-1"
                        >⠿</span>
                        <div>
                            <p class="text-sm font-medium text-gray-900">
                                {{ sectionLabels[section.section_type] || section.section_type }}
                            </p>
                            <p class="text-xs text-gray-400" v-if="section.title">{{ section.title }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <button
                            v-if="editableTypes.includes(section.section_type)"
                            type="button"
                            @click.stop="openEdit(section)"
                            class="text-sm text-brand-600 hover:underline"
                        >
                            Edit
                        </button>

                        <button
                            type="button"
                            :disabled="pendingIds.has(section.id)"
                            @click.stop="toggle(section)"
                            :class="[
                                'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                                section.is_visible ? 'bg-brand-600' : 'bg-gray-300',
                                pendingIds.has(section.id) ? 'opacity-50 cursor-wait' : '',
                            ]"
                        >
                            <span
                                :class="[
                                    'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                                    section.is_visible ? 'translate-x-6' : 'translate-x-1',
                                ]"
                            />
                        </button>
                    </div>
                </div>
            </div>

            <div v-if="page.props.flash?.success" class="mt-4 bg-green-100 text-green-800 px-4 py-2 rounded text-sm">
                {{ page.props.flash.success }}
            </div>
        </div>

        <!-- Edit form modal -->
        <div v-if="showForm" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="showForm = false">
            <div
                ref="modalRef"
                role="dialog"
                aria-modal="true"
                aria-labelledby="section-form-title"
                class="bg-white rounded-lg shadow-xl w-full p-6 max-h-[90vh] overflow-auto"
                :class="editingSection?.section_type === 'why_us' ? 'max-w-2xl' : 'max-w-lg'"
            >
                <h2 id="section-form-title" class="text-lg font-semibold mb-4">
                    Edit {{ sectionLabels[editingSection?.section_type] || editingSection?.section_type }}
                </h2>
                <form @submit.prevent="submit" class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Judul</label>
                        <input v-model="form.title" type="text" required class="w-full border rounded-md px-3 py-2 text-sm" />
                    </div>

                    <template v-if="editingSection?.section_type === 'why_us'">
                        <div class="flex items-center justify-between pt-1">
                            <label class="block text-sm font-medium">Kartu ("Why Us")</label>
                            <button type="button" @click="addItem" class="text-sm bg-brand-600 text-white px-3 py-1.5 rounded-md hover:bg-brand-700">
                                + Tambah Kartu
                            </button>
                        </div>
                        <p v-if="!form.items.length" class="text-sm text-gray-400 text-center py-6 border border-dashed rounded-md">
                            Belum ada kartu. Klik "Tambah Kartu" untuk menambahkan.
                        </p>
                        <div v-for="(item, index) in form.items" :key="index" class="border border-gray-200 rounded-md p-4 relative space-y-2">
                            <button type="button" @click="removeItem(index)" class="absolute top-3 right-3 text-xs text-red-600 hover:underline">
                                Hapus
                            </button>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pr-16">
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Judul Kartu</label>
                                    <input v-model="item.title" type="text" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="mis. Desain Custom" />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Icon</label>
                                    <select v-model="item.icon" class="w-full border rounded-md px-3 py-2 text-sm">
                                        <option value="">- Pilih icon -</option>
                                        <option v-for="opt in ICON_OPTIONS" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                                    </select>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi Kartu</label>
                                <textarea v-model="item.description" rows="2" class="w-full border rounded-md px-3 py-2 text-sm"></textarea>
                            </div>
                        </div>
                    </template>

                    <template v-else-if="editingSection?.section_type === 'hero'">
                        <div>
                            <label class="block text-sm font-medium mb-1">Eyebrow (teks kecil di atas judul)</label>
                            <input v-model="form.eyebrow" type="text" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="Jasa Website Profesional" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Subjudul</label>
                            <textarea v-model="form.subtitle" rows="3" class="w-full border rounded-md px-3 py-2 text-sm"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Gambar</label>
                            <p class="text-xs text-gray-500 mb-2">Ditampilkan sebagai background di belakang teks. Kalau kosong, background solid gelap seperti sekarang akan tetap dipakai.</p>
                            <MediaPicker v-model="form.image" />
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-sm font-medium mb-1">Teks Tombol Utama</label>
                                <input v-model="form.cta_primary_text" type="text" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="Konsultasi Gratis" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Link Tombol Utama</label>
                                <input v-model="form.cta_primary_url" type="text" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="/konsultasi" />
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-sm font-medium mb-1">Teks Tombol Kedua</label>
                                <input v-model="form.cta_secondary_text" type="text" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="Lihat Portfolio" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Link Tombol Kedua</label>
                                <input v-model="form.cta_secondary_url" type="text" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="/portfolio" />
                            </div>
                        </div>
                    </template>

                    <template v-else>
                        <div>
                            <label class="block text-sm font-medium mb-1">Gambar</label>
                            <MediaPicker v-model="form.image" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Deskripsi</label>
                            <textarea v-model="form.description" rows="5" class="w-full border rounded-md px-3 py-2 text-sm"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-sm font-medium mb-1">Teks Tombol</label>
                                <input v-model="form.button_text" type="text" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="Lihat Selengkapnya" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium mb-1">Link Tombol</label>
                                <input v-model="form.button_url" type="text" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="/tentang-kami" />
                            </div>
                        </div>
                    </template>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showForm = false" class="px-4 py-2 text-sm text-gray-600">Batal</button>
                        <button type="submit" class="bg-brand-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-brand-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>