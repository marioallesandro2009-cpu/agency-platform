<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import EmptyState from "@/Components/Admin/EmptyState.vue";
import { useForm, router, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import { useEscapeClose } from "@/composables/useEscapeClose";
import { useFocusTrap } from "@/composables/useFocusTrap";
import { confirmDialog } from "@/composables/useConfirmDialog";

const props = defineProps({
    plans: Array,
});

const page = usePage();
const showForm = ref(false);
const editingId = ref(null);
const modalRef = ref(null);

useEscapeClose(() => (showForm.value = false));
useFocusTrap(showForm, modalRef);

const form = useForm({
    name: "",
    price: "",
    features: "",
    cta_text: "Tanya Lebih Lanjut",
    order: 0,
});

function openCreate() {
    editingId.value = null;
    form.reset();
    form.cta_text = "Tanya Lebih Lanjut";
    showForm.value = true;
}

function openEdit(plan) {
    editingId.value = plan.id;
    form.name = plan.name;
    form.price = plan.price;
    form.features = (plan.features || []).join(", ");
    form.cta_text = plan.cta_text;
    form.order = plan.order;
    showForm.value = true;
}

function submit() {
    const payload = {
        ...form.data(),
        features: form.features.split(",").map((f) => f.trim()).filter(Boolean),
    };

    if (editingId.value) {
        router.put(route("admin.maintenance-plans.update", editingId.value), payload, {
            onSuccess: () => (showForm.value = false),
        });
    } else {
        router.post(route("admin.maintenance-plans.store"), payload, {
            onSuccess: () => (showForm.value = false),
        });
    }
}

function togglePublish(plan) {
    router.patch(route("admin.maintenance-plans.toggle-publish", plan.id), {}, { preserveScroll: true });
}

async function destroyPlan(plan) {
    if (await confirmDialog({ message: `Hapus paket "${plan.name}"?`, confirmLabel: "Hapus", danger: true })) {
        router.delete(route("admin.maintenance-plans.destroy", plan.id), { preserveScroll: true });
    }
}

function formatPrice(price) {
    return "Rp " + Number(price).toLocaleString("id-ID") + "/bulan";
}
</script>

<template>
    <AdminLayout>
        <template #header>Maintenance</template>

        <div v-if="page.props.flash?.success" class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded">
            {{ page.props.flash.success }}
        </div>

        <p class="text-sm text-gray-500 mb-4">
            Paket maintenance lanjutan yang ditampilkan di halaman Harga, berlaku sama untuk semua kategori paket website.
        </p>

        <button @click="openCreate" class="mb-4 bg-brand-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-brand-700">
            + Tambah Paket Maintenance
        </button>

        <div class="bg-white rounded-lg shadow divide-y">
            <EmptyState v-if="!plans.length" message="Belum ada paket maintenance." />
            <div v-for="plan in plans" :key="plan.id" class="flex items-center justify-between px-5 py-4">
                <div>
                    <p class="font-medium text-gray-900">{{ plan.name }}</p>
                    <p class="text-sm text-gray-500">{{ formatPrice(plan.price) }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        @click="togglePublish(plan)"
                        :class="plan.is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                        class="text-xs font-medium px-3 py-1 rounded-full"
                    >
                        {{ plan.is_published ? "Published" : "Draft" }}
                    </button>
                    <button @click="openEdit(plan)" class="text-sm text-brand-600 hover:underline">Edit</button>
                    <button @click="destroyPlan(plan)" class="text-sm text-red-600 hover:underline">Hapus</button>
                </div>
            </div>
        </div>

        <!-- Form modal -->
        <div v-if="showForm" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="showForm = false">
            <div ref="modalRef" role="dialog" aria-modal="true" aria-labelledby="maintenance-form-title" class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6 max-h-[90vh] overflow-auto">
                <h2 id="maintenance-form-title" class="text-lg font-semibold mb-4">{{ editingId ? "Edit" : "Tambah" }} Paket Maintenance</h2>
                <form @submit.prevent="submit" class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama Paket</label>
                        <input v-model="form.name" type="text" required class="w-full border rounded-md px-3 py-2 text-sm" placeholder="mis. Basic" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Harga per Bulan (angka)</label>
                        <input v-model="form.price" type="number" required class="w-full border rounded-md px-3 py-2 text-sm" placeholder="100000" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Benefit / Checklist (pisahkan koma)</label>
                        <input v-model="form.features" type="text" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="Backup rutin, Update plugin/sistem" />
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium mb-1">Teks CTA</label>
                            <input v-model="form.cta_text" type="text" class="w-full border rounded-md px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Urutan</label>
                            <input v-model="form.order" type="number" class="w-full border rounded-md px-3 py-2 text-sm" />
                        </div>
                    </div>

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
