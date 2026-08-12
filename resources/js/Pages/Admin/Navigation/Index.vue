<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import EmptyState from "@/Components/Admin/EmptyState.vue";
import { router, usePage } from "@inertiajs/vue3";
import { reactive, ref, watch } from "vue";
import { useEscapeClose } from "@/composables/useEscapeClose";
import { useFocusTrap } from "@/composables/useFocusTrap";
import { confirmDialog } from "@/composables/useConfirmDialog";

const props = defineProps({
    navItems: Array,
});

const page = usePage();
const localItems = ref([...props.navItems]);
const pendingIds = ref(new Set());
const showForm = ref(false);
const editingId = ref(null);
const modalRef = ref(null);

useEscapeClose(() => (showForm.value = false));
useFocusTrap(showForm, modalRef);

const form = reactive({ label: "", url: "" });

watch(
    () => props.navItems,
    (newVal) => {
        localItems.value = [...newVal];
    }
);

function openCreate() {
    editingId.value = null;
    Object.assign(form, { label: "", url: "" });
    showForm.value = true;
}

function openEdit(item) {
    editingId.value = item.id;
    Object.assign(form, { label: item.label, url: item.url });
    showForm.value = true;
}

function submit() {
    if (editingId.value) {
        router.put(route("admin.navigation.update", editingId.value), form, { onSuccess: () => (showForm.value = false) });
    } else {
        router.post(route("admin.navigation.store"), form, { onSuccess: () => (showForm.value = false) });
    }
}

async function destroyItem(item) {
    if (await confirmDialog({ message: `Hapus menu "${item.label}"?`, confirmLabel: "Hapus", danger: true })) {
        router.delete(route("admin.navigation.destroy", item.id), { preserveScroll: true });
    }
}

function toggle(item) {
    if (pendingIds.value.has(item.id)) return;

    const index = localItems.value.findIndex((i) => i.id === item.id);
    if (index === -1) return;

    const previousValue = localItems.value[index].is_visible;
    localItems.value[index].is_visible = !previousValue;
    pendingIds.value.add(item.id);

    router.patch(
        route("admin.navigation.toggle", item.id),
        {},
        {
            preserveState: true,
            preserveScroll: true,
            onError: () => {
                localItems.value[index].is_visible = previousValue;
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

    router.post(route("admin.navigation.reorder"), { order: items.map((i) => i.id) }, { preserveState: true, preserveScroll: true });
}
</script>

<template>
    <AdminLayout>
        <template #header>Navigasi</template>

        <div v-if="page.props.flash?.success" class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded">
            {{ page.props.flash.success }}
        </div>

        <div class="max-w-2xl">
            <p class="text-sm text-gray-500 mb-4">
                Seret ikon di kiri untuk mengubah urutan menu navbar publik. Menu "Jasa &amp; Layanan" (dropdown) diatur otomatis dari data Layanan.
            </p>

            <button @click="openCreate" class="mb-4 bg-brand-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-brand-700">
                + Tambah Menu
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
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-gray-900">{{ item.label }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ item.url }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 flex-shrink-0">
                        <button @click="openEdit(item)" class="text-sm text-brand-600 hover:underline">Edit</button>
                        <button @click="destroyItem(item)" class="text-sm text-red-600 hover:underline">Hapus</button>
                        <button
                            type="button"
                            :disabled="pendingIds.has(item.id)"
                            @click.stop="toggle(item)"
                            :class="[
                                'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                                item.is_visible ? 'bg-brand-600' : 'bg-gray-300',
                                pendingIds.has(item.id) ? 'opacity-50 cursor-wait' : '',
                            ]"
                        >
                            <span
                                :class="[
                                    'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                                    item.is_visible ? 'translate-x-6' : 'translate-x-1',
                                ]"
                            />
                        </button>
                    </div>
                </div>
                <EmptyState v-if="!localItems.length" message="Belum ada menu." />
            </div>
        </div>

        <div v-if="showForm" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="showForm = false">
            <div ref="modalRef" role="dialog" aria-modal="true" aria-labelledby="nav-form-title" class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                <h2 id="nav-form-title" class="text-lg font-semibold mb-4">{{ editingId ? "Edit" : "Tambah" }} Menu</h2>
                <form @submit.prevent="submit" class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Label</label>
                        <input v-model="form.label" type="text" required class="w-full border rounded-md px-3 py-2 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">URL</label>
                        <input v-model="form.url" type="text" required placeholder="/halaman-tujuan" class="w-full border rounded-md px-3 py-2 text-sm" />
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
