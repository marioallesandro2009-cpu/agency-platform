<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import EmptyState from "@/Components/Admin/EmptyState.vue";
import { router, usePage } from "@inertiajs/vue3";
import { reactive, ref } from "vue";
import { useEscapeClose } from "@/composables/useEscapeClose";
import { useFocusTrap } from "@/composables/useFocusTrap";
import { confirmDialog } from "@/composables/useConfirmDialog";

const props = defineProps({
    testimonials: Array,
});

const page = usePage();
const showForm = ref(false);
const editingId = ref(null);
const modalRef = ref(null);

useEscapeClose(() => (showForm.value = false));
useFocusTrap(showForm, modalRef);

const form = reactive({
    name: "",
    company: "",
    position: "",
    testimonial: "",
    rating: 5,
    order: 0,
});

function openCreate() {
    editingId.value = null;
    Object.assign(form, { name: "", company: "", position: "", testimonial: "", rating: 5, order: 0 });
    showForm.value = true;
}

function openEdit(t) {
    editingId.value = t.id;
    Object.assign(form, {
        name: t.name, company: t.company, position: t.position,
        testimonial: t.testimonial, rating: t.rating, order: t.order,
    });
    showForm.value = true;
}

function submit() {
    if (editingId.value) {
        router.put(route("admin.testimonials.update", editingId.value), { ...form }, {
            onSuccess: () => (showForm.value = false),
        });
    } else {
        router.post(route("admin.testimonials.store"), { ...form }, {
            onSuccess: () => (showForm.value = false),
        });
    }
}

function togglePublish(t) {
    router.patch(route("admin.testimonials.toggle-publish", t.id), {}, { preserveScroll: true });
}

async function destroyItem(t) {
    if (await confirmDialog({ message: `Hapus testimonial dari "${t.name}"?`, confirmLabel: "Hapus", danger: true })) {
        router.delete(route("admin.testimonials.destroy", t.id), { preserveScroll: true });
    }
}
</script>

<template>
    <AdminLayout>
        <template #header>Testimonials</template>

        <div v-if="page.props.flash?.success" class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded">
            {{ page.props.flash.success }}
        </div>

        <button @click="openCreate" class="mb-4 bg-brand-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-brand-700">
            + Tambah Testimonial
        </button>

        <div class="bg-white rounded-lg shadow divide-y">
            <EmptyState v-if="!testimonials.length" message="Belum ada testimonial." />
            <div v-for="t in testimonials" :key="t.id" class="flex items-start justify-between px-5 py-4 gap-4">
                <div class="flex-1">
                    <p class="font-medium text-gray-900">{{ t.name }} <span class="text-gray-400 font-normal text-sm">— {{ t.position }}, {{ t.company }}</span></p>
                    <p class="text-sm text-gray-600 mt-1">{{ t.testimonial }}</p>
                    <p class="text-xs text-yellow-500 mt-1">{{ "★".repeat(t.rating) }}{{ "☆".repeat(5 - t.rating) }}</p>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    <button
                        @click="togglePublish(t)"
                        :class="t.is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                        class="text-xs font-medium px-3 py-1 rounded-full"
                    >
                        {{ t.is_published ? "Published" : "Draft" }}
                    </button>
                    <button @click="openEdit(t)" class="text-sm text-brand-600 hover:underline">Edit</button>
                    <button @click="destroyItem(t)" class="text-sm text-red-600 hover:underline">Hapus</button>
                </div>
            </div>
        </div>

        <div v-if="showForm" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="showForm = false">
            <div ref="modalRef" role="dialog" aria-modal="true" aria-labelledby="testimonial-form-title" class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                <h2 id="testimonial-form-title" class="text-lg font-semibold mb-4">{{ editingId ? "Edit" : "Tambah" }} Testimonial</h2>
                <form @submit.prevent="submit" class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama</label>
                        <input v-model="form.name" type="text" required class="w-full border rounded-md px-3 py-2 text-sm" />
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium mb-1">Perusahaan</label>
                            <input v-model="form.company" type="text" class="w-full border rounded-md px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Jabatan</label>
                            <input v-model="form.position" type="text" class="w-full border rounded-md px-3 py-2 text-sm" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Isi Testimonial</label>
                        <textarea v-model="form.testimonial" required class="w-full border rounded-md px-3 py-2 text-sm" rows="3"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium mb-1">Rating (1-5)</label>
                            <input v-model="form.rating" type="number" min="1" max="5" class="w-full border rounded-md px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Urutan</label>
                            <input v-model="form.order" type="number" class="w-full border rounded-md px-3 py-2 text-sm" />
                        </div>
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