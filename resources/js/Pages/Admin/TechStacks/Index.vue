<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import EmptyState from "@/Components/Admin/EmptyState.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import { useEscapeClose } from "@/composables/useEscapeClose";
import { useFocusTrap } from "@/composables/useFocusTrap";
import { confirmDialog } from "@/composables/useConfirmDialog";

const props = defineProps({
    techStacks: Array,
});

const page = usePage();
const localItems = ref([...props.techStacks]);
const pendingIds = ref(new Set());
const showForm = ref(false);
const editingId = ref(null);
const modalRef = ref(null);
const iconInput = ref(null);

useEscapeClose(() => (showForm.value = false));
useFocusTrap(showForm, modalRef);

const form = useForm({
    name: "",
    link_url: "",
    is_active: true,
    icon: null,
});

watch(
    () => props.techStacks,
    (newVal) => {
        localItems.value = [...newVal];
    }
);

function openCreate() {
    editingId.value = null;
    form.reset();
    form.is_active = true;
    if (iconInput.value) iconInput.value.value = "";
    showForm.value = true;
}

function openEdit(item) {
    editingId.value = item.id;
    form.reset();
    form.name = item.name;
    form.link_url = item.link_url || "";
    form.is_active = item.is_active;
    form.icon = null;
    if (iconInput.value) iconInput.value.value = "";
    showForm.value = true;
}

function submit() {
    if (editingId.value) {
        form.transform((data) => ({ ...data, _method: "put" })).post(route("admin.tech-stacks.update", editingId.value), {
            forceFormData: true,
            onSuccess: () => (showForm.value = false),
        });
    } else {
        form.post(route("admin.tech-stacks.store"), {
            forceFormData: true,
            onSuccess: () => (showForm.value = false),
        });
    }
}

async function destroyItem(item) {
    if (await confirmDialog({ message: `Hapus tech stack "${item.name}"?`, confirmLabel: "Hapus", danger: true })) {
        router.delete(route("admin.tech-stacks.destroy", item.id), { preserveScroll: true });
    }
}

function toggleActive(item) {
    if (pendingIds.value.has(item.id)) return;

    const index = localItems.value.findIndex((i) => i.id === item.id);
    if (index === -1) return;

    const previousValue = localItems.value[index].is_active;
    localItems.value[index].is_active = !previousValue;
    pendingIds.value.add(item.id);

    router.patch(
        route("admin.tech-stacks.toggle-active", item.id),
        {},
        {
            preserveState: true,
            preserveScroll: true,
            onError: () => {
                localItems.value[index].is_active = previousValue;
            },
            onFinish: () => {
                pendingIds.value.delete(item.id);
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

    const items = [...localItems.value];
    const [moved] = items.splice(draggedIndex.value, 1);
    items.splice(index, 0, moved);
    localItems.value = items;
    draggedIndex.value = null;

    router.post(route("admin.tech-stacks.reorder"), { order: items.map((i) => i.id) }, { preserveState: true, preserveScroll: true });
}
</script>

<template>
    <AdminLayout>
        <template #header>Tech Stack</template>

        <div v-if="page.props.flash?.success" class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded">
            {{ page.props.flash.success }}
        </div>

        <div class="max-w-2xl">
            <p class="text-sm text-gray-500 mb-4">
                Seret ikon di kiri untuk mengubah urutan logo di section "Teknologi yang Kami Gunakan" (halaman Tentang Kami).
            </p>

            <button @click="openCreate" class="mb-4 bg-brand-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-brand-700">
                + Tambah Tech Stack
            </button>

            <div class="bg-white border border-gray-200 rounded-lg divide-y">
                <div
                    v-for="(item, index) in localItems"
                    :key="item.id"
                    @dragover.prevent
                    @drop="onDrop(index)"
                    class="flex items-center justify-between px-4 py-3 hover:bg-gray-50"
                >
                    <div class="flex items-center gap-3 min-w-0">
                        <span draggable="true" @dragstart="onDragStart(index)" class="text-gray-400 cursor-move select-none px-1">⠿</span>
                        <div class="w-8 h-8 rounded-md border border-gray-100 flex items-center justify-center flex-shrink-0 bg-gray-50">
                            <img :src="item.icon_url" :alt="item.name" width="24" height="24" class="w-6 h-6 object-contain" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate">{{ item.name }}</p>
                            <p v-if="item.link_url" class="text-xs text-gray-400 truncate">{{ item.link_url }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 flex-shrink-0">
                        <button @click="openEdit(item)" class="text-sm text-brand-600 hover:underline">Edit</button>
                        <button @click="destroyItem(item)" class="text-sm text-red-600 hover:underline">Hapus</button>
                        <button
                            type="button"
                            :disabled="pendingIds.has(item.id)"
                            @click.stop="toggleActive(item)"
                            :class="[
                                'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                                item.is_active ? 'bg-brand-600' : 'bg-gray-300',
                                pendingIds.has(item.id) ? 'opacity-50 cursor-wait' : '',
                            ]"
                        >
                            <span
                                :class="[
                                    'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                                    item.is_active ? 'translate-x-6' : 'translate-x-1',
                                ]"
                            />
                        </button>
                    </div>
                </div>
                <EmptyState v-if="!localItems.length" message="Belum ada tech stack." />
            </div>
        </div>

        <div v-if="showForm" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="showForm = false">
            <div ref="modalRef" role="dialog" aria-modal="true" aria-labelledby="techstack-form-title" class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h2 id="techstack-form-title" class="text-lg font-semibold mb-4">{{ editingId ? "Edit" : "Tambah" }} Tech Stack</h2>
                <form @submit.prevent="submit" class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama</label>
                        <input v-model="form.name" type="text" required class="w-full border rounded-md px-3 py-2 text-sm" />
                        <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Link (opsional)</label>
                        <input v-model="form.link_url" type="url" placeholder="https://..." class="w-full border rounded-md px-3 py-2 text-sm" />
                        <p v-if="form.errors.link_url" class="text-xs text-red-500 mt-1">{{ form.errors.link_url }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">
                            Logo (SVG/PNG) <span v-if="!editingId" class="text-red-500">*</span>
                        </label>
                        <input
                            ref="iconInput"
                            type="file"
                            accept=".svg,.png,image/svg+xml,image/png"
                            :required="!editingId"
                            @change="form.icon = $event.target.files[0] || null"
                            class="w-full border rounded-md px-3 py-2 text-sm"
                        />
                        <p v-if="editingId" class="text-xs text-gray-400 mt-1">Kosongkan untuk mempertahankan logo yang sudah ada.</p>
                        <p v-if="form.errors.icon" class="text-xs text-red-500 mt-1">{{ form.errors.icon }}</p>
                    </div>
                    <label class="flex items-center gap-2 text-sm">
                        <input v-model="form.is_active" type="checkbox" />
                        Aktif (tampil di halaman Tentang Kami)
                    </label>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showForm = false" class="px-4 py-2 text-sm text-gray-600">Batal</button>
                        <button type="submit" :disabled="form.processing" class="bg-brand-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-brand-700">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
