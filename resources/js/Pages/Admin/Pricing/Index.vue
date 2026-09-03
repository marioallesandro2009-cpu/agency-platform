<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import EmptyState from "@/Components/Admin/EmptyState.vue";
import MediaPicker from "@/Components/MediaPicker.vue";
import { useForm, router, usePage } from "@inertiajs/vue3";
import { ref } from "vue";
import { useEscapeClose } from "@/composables/useEscapeClose";
import { useFocusTrap } from "@/composables/useFocusTrap";
import { confirmDialog } from "@/composables/useConfirmDialog";
import { PRICING_CATEGORIES, PRICING_TIERS } from "@/constants/pricing";

const props = defineProps({
    packages: Array,
});

const page = usePage();
const showForm = ref(false);
const editingId = ref(null);
const modalRef = ref(null);

useEscapeClose(() => (showForm.value = false));
useFocusTrap(showForm, modalRef);

const form = useForm({
    name: "",
    category: "",
    tier: "",
    tagline: "",
    description: "",
    image: "",
    price: "",
    price_prefix: "",
    price_suffix: "",
    renewal_price: "",
    estimated_days: "",
    value_note: "",
    features: "",
    comparisonFeatures: "",
    badge: "",
    is_recommended: false,
    cta_text: "Pesan Website",
    cta_url: "/konsultasi",
    order: 0,
});

function openCreate() {
    editingId.value = null;
    form.reset();
    form.cta_text = "Pesan Website";
    form.cta_url = "/konsultasi";
    showForm.value = true;
}

function openEdit(pkg) {
    editingId.value = pkg.id;
    form.name = pkg.name;
    form.category = pkg.category || "";
    form.tier = pkg.tier || "";
    form.tagline = pkg.tagline || "";
    form.description = pkg.description;
    form.image = pkg.image || "";
    form.price = pkg.price;
    form.price_prefix = pkg.price_prefix;
    form.price_suffix = pkg.price_suffix;
    form.renewal_price = pkg.renewal_price;
    form.estimated_days = pkg.estimated_days || "";
    form.value_note = pkg.value_note || "";
    form.features = (pkg.features || []).map((f) => (f.note ? `${f.label}: ${f.note}` : f.label)).join("\n");
    form.comparisonFeatures = (pkg.comparison_features || []).map((row) => `${row.feature}: ${row.value}`).join("\n");
    form.badge = pkg.badge;
    form.is_recommended = pkg.is_recommended;
    form.cta_text = pkg.cta_text;
    form.cta_url = pkg.cta_url;
    form.order = pkg.order;
    showForm.value = true;
}

function categoryLabel(value) {
    return PRICING_CATEGORIES.find((c) => c.value === value)?.label || value || "-";
}

function tierLabel(value) {
    return PRICING_TIERS.find((t) => t.value === value)?.label || value || "-";
}

function submit() {
    const { comparisonFeatures, ...rest } = form.data();
    const payload = {
        ...rest,
        features: form.features
            .split("\n")
            .map((line) => line.trim())
            .filter(Boolean)
            .map((line) => {
                const [label, ...noteParts] = line.split(":");
                const note = noteParts.join(":").trim();
                return { label: label.trim(), note: note || null };
            })
            .filter((row) => row.label),
        comparison_features: comparisonFeatures
            .split("\n")
            .map((line) => line.trim())
            .filter(Boolean)
            .map((line) => {
                const [feature, ...valueParts] = line.split(":");
                return { feature: feature.trim(), value: valueParts.join(":").trim() };
            })
            .filter((row) => row.feature),
    };

    if (editingId.value) {
        router.put(route("admin.pricing.update", editingId.value), payload, {
            onSuccess: () => (showForm.value = false),
        });
    } else {
        router.post(route("admin.pricing.store"), payload, {
            onSuccess: () => (showForm.value = false),
        });
    }
}

function togglePublish(pkg) {
    router.patch(route("admin.pricing.toggle-publish", pkg.id), {}, { preserveScroll: true });
}

async function destroyPkg(pkg) {
    if (await confirmDialog({ message: `Hapus paket "${pkg.name}"?`, confirmLabel: "Hapus", danger: true })) {
        router.delete(route("admin.pricing.destroy", pkg.id), { preserveScroll: true });
    }
}

function formatPrice(price) {
    if (!price) return "Custom";
    return "Rp " + Number(price).toLocaleString("id-ID");
}
</script>

<template>
    <AdminLayout>
        <template #header>Pricing</template>

        <div v-if="page.props.flash?.success" class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded">
            {{ page.props.flash.success }}
        </div>

        <button @click="openCreate" class="mb-4 bg-brand-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-brand-700">
            + Tambah Paket
        </button>

        <div class="bg-white rounded-lg shadow divide-y">
            <EmptyState v-if="!packages.length" message="Belum ada paket harga." />
            <div v-for="pkg in packages" :key="pkg.id" class="flex items-center justify-between px-5 py-4">
                <div>
                    <p class="font-medium text-gray-900">
                        {{ pkg.name }}
                        <span class="ml-2 text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full">{{ categoryLabel(pkg.category) }} / {{ tierLabel(pkg.tier) }}</span>
                        <span v-if="pkg.is_recommended" class="ml-2 text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">Recommended</span>
                    </p>
                    <p class="text-sm text-gray-500">{{ formatPrice(pkg.price) }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        @click="togglePublish(pkg)"
                        :class="pkg.is_published ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'"
                        class="text-xs font-medium px-3 py-1 rounded-full"
                    >
                        {{ pkg.is_published ? "Published" : "Draft" }}
                    </button>
                    <button @click="openEdit(pkg)" class="text-sm text-brand-600 hover:underline">Edit</button>
                    <button @click="destroyPkg(pkg)" class="text-sm text-red-600 hover:underline">Hapus</button>
                </div>
            </div>
        </div>

        <!-- Form modal -->
        <div v-if="showForm" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="showForm = false">
            <div ref="modalRef" role="dialog" aria-modal="true" aria-labelledby="pricing-form-title" class="bg-white rounded-lg shadow-xl max-w-lg w-full p-6 max-h-[90vh] overflow-auto">
                <h2 id="pricing-form-title" class="text-lg font-semibold mb-4">{{ editingId ? "Edit" : "Tambah" }} Paket Harga</h2>
                <form @submit.prevent="submit" class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Nama Paket</label>
                        <input v-model="form.name" type="text" required class="w-full border rounded-md px-3 py-2 text-sm" placeholder="mis. Company Profile - Seed" />
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium mb-1">Kategori</label>
                            <select v-model="form.category" required class="w-full border rounded-md px-3 py-2 text-sm">
                                <option value="" disabled>- Pilih kategori -</option>
                                <option v-for="cat in PRICING_CATEGORIES" :key="cat.value" :value="cat.value">{{ cat.label }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Tier</label>
                            <select v-model="form.tier" required class="w-full border rounded-md px-3 py-2 text-sm">
                                <option value="" disabled>- Pilih tier -</option>
                                <option v-for="tier in PRICING_TIERS" :key="tier.value" :value="tier.value">{{ tier.label }}</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tagline (khusus kategori ini)</label>
                        <input v-model="form.tagline" type="text" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="mis. Mulai bangun kredibilitas" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Deskripsi</label>
                        <textarea v-model="form.description" class="w-full border rounded-md px-3 py-2 text-sm" rows="2"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Gambar</label>
                        <MediaPicker v-model="form.image" />
                    </div>
                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block text-sm font-medium mb-1">Prefix</label>
                            <input v-model="form.price_prefix" type="text" class="w-full border rounded-md px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Harga (angka)</label>
                            <input v-model="form.price" type="number" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="kosongkan jika custom" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Suffix</label>
                            <input v-model="form.price_suffix" type="text" class="w-full border rounded-md px-3 py-2 text-sm" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Harga Renewal per Tahun (angka)</label>
                        <input v-model="form.renewal_price" type="number" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="mis. 400000" />
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium mb-1">Estimasi Pengerjaan</label>
                            <input v-model="form.estimated_days" type="text" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="mis. 7 hari kerja" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Catatan Nilai (di bawah harga)</label>
                            <input v-model="form.value_note" type="text" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="Termasuk domain, hosting, dan email bisnis senilai Rp650rb" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Benefit / Checklist (1 baris per fitur, format "Label: Catatan nilai")</label>
                        <textarea
                            v-model="form.features"
                            rows="6"
                            class="w-full border rounded-md px-3 py-2 text-sm font-mono"
                            placeholder="5 Halaman&#10;Domain + Hosting 1 Tahun: senilai Rp500rb, gratis tahun pertama&#10;Revisi desain 3x"
                        ></textarea>
                        <p class="text-xs text-gray-500 mt-1">
                            Catatan nilai (setelah titik dua) opsional — tampil di bawah label dengan font lebih kecil. Baris "Revisi" sebaiknya diletakkan paling akhir.
                        </p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Tabel Perbandingan Fitur (1 baris per fitur, format "Fitur: Nilai")</label>
                        <textarea
                            v-model="form.comparisonFeatures"
                            rows="6"
                            class="w-full border rounded-md px-3 py-2 text-sm font-mono"
                            placeholder="SSL Certificate: Gratis&#10;Jumlah Halaman: 5 Halaman&#10;Revisi Desain: 3x"
                        ></textarea>
                        <p class="text-xs text-gray-500 mt-1">
                            Gunakan label fitur yang sama persis di ketiga tier (Seed/Growth/Scale) kategori ini agar barisnya sejajar di tabel perbandingan halaman Harga.
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium mb-1">Badge</label>
                            <input v-model="form.badge" type="text" class="w-full border rounded-md px-3 py-2 text-sm" placeholder="Paling Populer" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Urutan</label>
                            <input v-model="form.order" type="number" class="w-full border rounded-md px-3 py-2 text-sm" />
                        </div>
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
                    <label class="flex items-center gap-2 text-sm">
                        <input v-model="form.is_recommended" type="checkbox" />
                        Tandai sebagai "Recommended"
                    </label>

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