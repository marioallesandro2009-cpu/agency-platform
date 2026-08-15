<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import EmptyState from "@/Components/Admin/EmptyState.vue";
import MediaPicker from "@/Components/MediaPicker.vue";
import { router, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import { confirmDialog } from "@/composables/useConfirmDialog";

const props = defineProps({
    projects: Array,
});

const showForm = ref(false);
const editingSlug = ref(null);

const form = useForm({
    name: "",
    slug: "",
    category: "",
    short_description: "",
    description: "",
    challenge: "",
    solution: "",
    features: "",
    technologies: "",
    client_label: "",
    project_type: "client",
    thumbnail: "",
    cover_image: "",
    gallery: "",
    live_url: "",
    seo_title: "",
    seo_description: "",
    is_featured: false,
    is_published: true,
    order: 0,
});

function openCreate() {
    form.reset();
    editingSlug.value = null;
    showForm.value = true;
}

function openEdit(project) {
    form.name = project.name;
    form.slug = project.slug;
    form.category = project.category || "";
    form.short_description = project.short_description || "";
    form.description = project.description || "";
    form.challenge = project.challenge || "";
    form.solution = project.solution || "";
    form.features = (project.features || []).join(", ");
    form.technologies = (project.technologies || []).join(", ");
    form.client_label = project.client_label || "";
    form.project_type = project.project_type || "client";
    form.thumbnail = project.thumbnail || "";
    form.cover_image = project.cover_image || "";
    form.gallery = (project.gallery || []).join(", ");
    form.live_url = project.live_url || "";
    form.seo_title = project.seo_title || "";
    form.seo_description = project.seo_description || "";
    form.is_featured = project.is_featured;
    form.is_published = project.is_published;
    form.order = project.order;
    editingSlug.value = project.slug;
    showForm.value = true;
}

function submit() {
    if (editingSlug.value) {
        form.put(route("admin.portfolio.update", editingSlug.value), {
            onSuccess: () => { showForm.value = false; },
        });
    } else {
        form.post(route("admin.portfolio.store"), {
            onSuccess: () => { showForm.value = false; },
        });
    }
}

function togglePublish(project) {
    router.patch(route("admin.portfolio.toggle-publish", project.slug), {}, { preserveScroll: true });
}

async function destroy(project) {
    if (await confirmDialog({ message: `Hapus proyek "${project.name}"?`, confirmLabel: "Hapus", danger: true })) {
        router.delete(route("admin.portfolio.destroy", project.slug), { preserveScroll: true });
    }
}
</script>

<template>
    <AdminLayout>
        <div class="max-w-5xl mx-auto py-8 px-4">
            <div class="flex items-center justify-between mb-6">
                <h1 class="text-xl font-semibold text-gray-900">Portfolio</h1>
                <button
                    @click="openCreate"
                    class="bg-brand-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-brand-700"
                >
                    + Tambah Proyek
                </button>
            </div>

            <!-- Form -->
            <div v-if="showForm" class="bg-white border border-gray-200 rounded-lg p-6 mb-6">
                <h2 class="font-semibold text-gray-900 mb-4">{{ editingSlug ? "Edit" : "Tambah" }} Proyek</h2>
                <form @submit.prevent="submit" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Nama Proyek</label>
                            <input v-model="form.name" type="text" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Slug (kosongkan untuk auto)</label>
                            <input v-model="form.slug" type="text" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Kategori</label>
                            <input v-model="form.category" type="text" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Client Label</label>
                            <input v-model="form.client_label" type="text" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" placeholder="Proyek Konsep / Demo" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Jenis Proyek</label>
                            <select v-model="form.project_type" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm">
                                <option value="client">Client Project</option>
                                <option value="personal">Personal/Internal Project</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi Singkat</label>
                        <input v-model="form.short_description" type="text" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" />
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Deskripsi Lengkap</label>
                        <textarea v-model="form.description" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Tantangan</label>
                            <textarea v-model="form.challenge" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Solusi</label>
                            <textarea v-model="form.solution" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"></textarea>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Fitur (pisahkan koma)</label>
                            <input v-model="form.features" type="text" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" placeholder="Fitur A, Fitur B, Fitur C" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Teknologi (pisahkan koma)</label>
                            <input v-model="form.technologies" type="text" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" placeholder="Laravel, Vue, MySQL" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Thumbnail</label>
                            <MediaPicker v-model="form.thumbnail" />
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Cover Image</label>
                            <MediaPicker v-model="form.cover_image" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Galeri (URL gambar, pisahkan koma)</label>
                        <input
                            v-model="form.gallery"
                            type="text"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm"
                            placeholder="https://.../1.jpg, https://.../2.jpg"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Link Kunjungi (Live URL)</label>
                        <input v-model="form.live_url" type="url" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" placeholder="https://..." />
                        <p v-if="form.errors.live_url" class="text-xs text-red-500 mt-1">{{ form.errors.live_url }}</p>
                        <p class="text-xs text-gray-400 mt-1">Isi ini untuk memunculkan tombol "Kunjungi" di halaman detail publik.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">Urutan</label>
                            <input v-model.number="form.order" type="number" class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm" />
                        </div>
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input v-model="form.is_featured" type="checkbox" /> Unggulan
                        </label>
                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input v-model="form.is_published" type="checkbox" /> Publikasikan
                        </label>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" :disabled="form.processing" class="bg-brand-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-brand-700">
                            Simpan
                        </button>
                        <button type="button" @click="showForm = false" class="text-gray-500 text-sm font-medium px-4 py-2">
                            Batal
                        </button>
                    </div>
                </form>
            </div>

            <!-- List -->
            <div class="bg-white border border-gray-200 rounded-lg divide-y">
                <EmptyState v-if="!projects.length" message="Belum ada portfolio." />
                <div v-for="project in projects" :key="project.id" class="flex items-center justify-between px-4 py-3">
                    <div>
                        <p class="text-sm font-medium text-gray-900 flex items-center gap-2">
                            {{ project.name }}
                            <span
                                v-if="project.project_type === 'personal'"
                                class="bg-gray-100 text-gray-600 text-[10px] font-semibold uppercase tracking-wide px-2 py-0.5 rounded-full"
                            >
                                Personal
                            </span>
                        </p>
                        <p class="text-xs text-gray-400">
                            {{ project.category }}
                            <span v-if="project.live_url"> Â· <a :href="project.live_url" target="_blank" class="text-brand-600 hover:underline">Link live</a></span>
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button
                            @click="togglePublish(project)"
                            :class="project.is_published ? 'text-green-600' : 'text-gray-400'"
                            class="text-xs font-medium"
                        >
                            {{ project.is_published ? "Published" : "Draft" }}
                        </button>
                        <button @click="openEdit(project)" class="text-brand-600 text-sm hover:underline">Edit</button>
                        <button @click="destroy(project)" class="text-red-600 text-sm hover:underline">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>