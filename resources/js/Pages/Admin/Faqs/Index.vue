<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import EmptyState from "@/Components/Admin/EmptyState.vue";
import { router, usePage } from "@inertiajs/vue3";
import { reactive, ref } from "vue";
import { useEscapeClose } from "@/composables/useEscapeClose";
import { useFocusTrap } from "@/composables/useFocusTrap";
import { confirmDialog } from "@/composables/useConfirmDialog";

const props = defineProps({
    faqs: Array,
});

const page = usePage();
const showForm = ref(false);
const editingId = ref(null);
const modalRef = ref(null);

useEscapeClose(() => (showForm.value = false));
useFocusTrap(showForm, modalRef);

const form = reactive({
    question: "",
    answer: "",
    category: "general",
    order: 0,
});

function openCreate() {
    editingId.value = null;
    Object.assign(form, { question: "", answer: "", category: "general", order: 0 });
    showForm.value = true;
}

function openEdit(f) {
    editingId.value = f.id;
    Object.assign(form, { question: f.question, answer: f.answer, category: f.category, order: f.order });
    showForm.value = true;
}

function submit() {
    if (editingId.value) {
        router.put(route("admin.faqs.update", editingId.value), { ...form }, { onSuccess: () => (showForm.value = false) });
    } else {
        router.post(route("admin.faqs.store"), { ...form }, { onSuccess: () => (showForm.value = false) });
    }
}

function togglePublish(f) {
    router.patch(route("admin.faqs.toggle-publish", f.id), {}, { preserveScroll: true });
}

async function destroyItem(f) {
    if (await confirmDialog({ message: "Hapus FAQ ini?", confirmLabel: "Hapus", danger: true })) {
        router.delete(route("admin.faqs.destroy", f.id), { preserveScroll: true });
    }
}
</script>

<template>
    <AdminLayout>
        <template #header>FAQ</template>

        <div v-if="page.props.flash?.success" class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded">
            {{ page.props.flash.success }}
        </div>

        <button @click="openCreate" class="mb-4 bg-brand-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-brand-700">
            + Tambah FAQ
        </button>

        <div class="bg-white rounded-lg shadow divide-y">
            <EmptyState v-if="!faqs.length" message="Belum ada FAQ." />
            <div v-for="f in faqs" :key="f.id" class="flex items-start justify-between px-5 py-4 gap-4">
                <div class="flex-1">
                    <p class="font-medium text-gray-900">{{ f.question }}</p>
                    <p class="text-sm text-gray-500 mt-1">{{ f.answer }}</p>
                    <span class="inline-block mt-2 text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full capitalize">{{ f.category }}</span>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                    <button
                        @click="togglePublish(f)"
                        :class="f.is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                        class="text-xs font-medium px-3 py-1 rounded-full"
                    >
                        {{ f.is_published ? "Published" : "Draft" }}
                    </button>
                    <button @click="openEdit(f)" class="text-sm text-brand-600 hover:underline">Edit</button>
                    <button @click="destroyItem(f)" class="text-sm text-red-600 hover:underline">Hapus</button>
                </div>
            </div>
        </div>

        <div v-if="showForm" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="showForm = false">
            <div ref="modalRef" role="dialog" aria-modal="true" aria-labelledby="faq-form-title" class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                <h2 id="faq-form-title" class="text-lg font-semibold mb-4">{{ editingId ? "Edit" : "Tambah" }} FAQ</h2>
                <form @submit.prevent="submit" class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Pertanyaan</label>
                        <input v-model="form.question" type="text" required class="w-full border rounded-md px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Jawaban</label>
                        <textarea v-model="form.answer" required class="w-full border rounded-md px-3 py-2 text-sm" rows="3"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium mb-1">Kategori</label>
                            <select v-model="form.category" class="w-full border rounded-md px-3 py-2 text-sm">
                                <option value="general">General</option>
                                <option value="pricing">Pricing</option>
                                <option value="website">Website</option>
                                <option value="domain">Domain</option>
                                <option value="hosting">Hosting</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="ecommerce">E-commerce</option>
                                <option value="payment">Payment</option>
                                <option value="seo">SEO</option>
                            </select>
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