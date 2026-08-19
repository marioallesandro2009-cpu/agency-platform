<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import MediaPicker from "@/Components/MediaPicker.vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import { reactive, ref, watch } from "vue";

const props = defineProps({
    settings: Object,
});

const page = usePage();
const tabs = ["general", "contact", "social", "branding", "integrations", "notifications"];
const activeTab = ref("general");
const imageKeys = ["logo", "logo_dark", "og_image"];
const placeholders = {
    ga_measurement_id: "G-XXXXXXXXXX",
};

const labels = {
    website_name: "Nama Website",
    tagline: "Tagline",
    description: "Deskripsi",
    logo: "Logo",
    logo_dark: "Logo Dark Mode",
    og_image: "OG Image",
    footer_disclaimer: "Disclaimer Footer (elemen visual)",
    whatsapp_number: "Nomor WhatsApp",
    email: "Email",
    phone: "Telepon",
    address: "Alamat",
    maps_url: "Google Maps URL",
    instagram: "Instagram",
    tiktok: "TikTok",
    facebook: "Facebook",
    linkedin: "LinkedIn",
    youtube: "YouTube",
    primary_color: "Warna Utama",
    secondary_color: "Warna Sekunder",
    ga_measurement_id: "Google Analytics Measurement ID",
    admin_notification_email: "Email Notifikasi Admin",
};

// "favicon" is deliberately not part of `forms.general.values` — it goes
// through its own dedicated upload widget/endpoint below (needs file +
// dimension validation the generic string-values form can't express), not
// the generic PUT /admin/settings. See SettingController::updateFavicon().
const forms = {};
tabs.forEach((tab) => {
    const values = { ...props.settings[tab] };
    delete values.favicon;
    forms[tab] = useForm({ group: tab, values });
});

const submit = (tab) => {
    forms[tab].put(route("admin.settings.update"), {
        preserveScroll: true,
    });
};

const favicon = reactive({
    current: props.settings.general?.favicon || null,
    file: null,
    previewUrl: null,
    warning: "",
    saving: false,
    deleting: false,
});

// Same fix as the /admin/maskot preview bug: the local `favicon` state is
// only built once at mount, so without this watcher a fresh value from the
// server after upload/delete wouldn't reach the already-mounted component.
watch(
    () => props.settings.general?.favicon,
    (fresh) => {
        favicon.current = fresh || null;
    }
);

// Warns (non-blocking) when the picked image isn't square or is smaller
// than 180px — the size that covers both Google's search-result favicon
// minimum (48px) and Apple's apple-touch-icon recommendation from a single
// file. Actual enforcement happens server-side in updateFavicon(); this is
// just instant feedback before the upload round-trip. .ico decode support
// varies by browser, so a failed/unsupported read fails open (no warning)
// rather than falsely blocking a valid file.
function checkFaviconDimensions(file) {
    return new Promise((resolve) => {
        const img = new Image();
        const url = URL.createObjectURL(file);
        const cleanup = (warning) => {
            URL.revokeObjectURL(url);
            resolve(warning);
        };
        img.onload = () => {
            if (img.naturalWidth !== img.naturalHeight) {
                cleanup("Gambar ini tidak persegi (lebar dan tinggi berbeda). Favicon sebaiknya berbentuk persegi.");
            } else if (img.naturalWidth < 180) {
                cleanup(`Gambar ini hanya ${img.naturalWidth}x${img.naturalHeight}px. Minimal 180x180px supaya tajam di semua ukuran.`);
            } else {
                cleanup("");
            }
        };
        img.onerror = () => cleanup("");
        img.src = url;
    });
}

function pickFavicon(event) {
    const file = event.target.files?.[0];
    if (!file) return;
    favicon.file = file;
    favicon.previewUrl = URL.createObjectURL(file);
    favicon.warning = "";
    checkFaviconDimensions(file).then((warning) => {
        favicon.warning = warning;
    });
}

function saveFavicon() {
    if (!favicon.file) return;
    favicon.saving = true;

    const formData = new FormData();
    formData.append("favicon", favicon.file);

    router.post(route("admin.settings.favicon.update"), formData, {
        preserveScroll: true,
        onSuccess: () => {
            favicon.file = null;
            favicon.previewUrl = null;
            favicon.warning = "";
        },
        onFinish: () => {
            favicon.saving = false;
        },
    });
}

function destroyFavicon() {
    if (!window.confirm("Hapus favicon custom ini? Situs akan kembali memakai favicon default.")) {
        return;
    }

    favicon.deleting = true;

    router.delete(route("admin.settings.favicon.destroy"), {
        preserveScroll: true,
        onSuccess: () => {
            favicon.file = null;
            favicon.previewUrl = null;
            favicon.warning = "";
        },
        onFinish: () => {
            favicon.deleting = false;
        },
    });
}
</script>

<template>
    <AdminLayout>
        <template #header>Settings</template>

        <div v-if="page.props.flash?.success" class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded">
            {{ page.props.flash.success }}
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="border-b flex gap-2 px-4">
                <button
                    v-for="tab in tabs"
                    :key="tab"
                    @click="activeTab = tab"
                    :class="activeTab === tab ? 'border-brand-600 text-brand-600' : 'border-transparent text-gray-500'"
                    class="capitalize px-4 py-3 border-b-2 text-sm font-medium hover:text-brand-600"
                >
                    {{ tab }}
                </button>
            </div>

            <div v-for="tab in tabs" :key="tab" v-show="activeTab === tab" class="p-6">
                <div v-if="tab === 'general'" class="max-w-xl mb-8 pb-8 border-b">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Favicon</label>
                    <p class="text-xs text-gray-500 mb-3">
                        Icon kecil di tab browser dan hasil pencarian Google. PNG atau ICO, persegi, minimal 180x180px, maks 2MB.
                    </p>

                    <div class="flex gap-4">
                        <div class="w-16 h-16 rounded-md border border-gray-200 bg-gray-50 flex-shrink-0 overflow-hidden flex items-center justify-center">
                            <img v-if="favicon.previewUrl || favicon.current" :src="favicon.previewUrl || favicon.current" class="w-full h-full object-contain" alt="" />
                            <span v-else class="text-[10px] text-gray-300 text-center px-1">Default</span>
                        </div>

                        <div class="flex-1 min-w-0 space-y-2">
                            <div class="flex flex-wrap items-center gap-2">
                                <label class="inline-flex items-center gap-2 text-xs bg-brand-600 text-white px-3 py-1.5 rounded-md cursor-pointer hover:bg-brand-700">
                                    Pilih File
                                    <input type="file" accept="image/png,.png,.ico" class="hidden" @change="pickFavicon" />
                                </label>
                                <button
                                    v-if="favicon.file"
                                    type="button"
                                    :disabled="favicon.saving"
                                    @click="saveFavicon"
                                    class="text-xs font-medium bg-brand-600 text-white px-3 py-1.5 rounded-md hover:bg-brand-700 disabled:opacity-50"
                                >
                                    {{ favicon.saving ? "Menyimpan..." : "Simpan Favicon" }}
                                </button>
                                <button
                                    v-if="favicon.current && !favicon.file"
                                    type="button"
                                    :disabled="favicon.deleting"
                                    @click="destroyFavicon"
                                    class="text-xs bg-red-50 text-red-700 border border-red-200 px-3 py-1.5 rounded-md hover:bg-red-100 disabled:opacity-50"
                                >
                                    {{ favicon.deleting ? "Menghapus..." : "Kembalikan ke Default" }}
                                </button>
                            </div>
                            <p v-if="favicon.warning" class="text-[11px] text-amber-800 bg-amber-50 border border-amber-200 rounded-md px-2 py-1.5">
                                <strong>Peringatan:</strong> {{ favicon.warning }}
                            </p>
                        </div>
                    </div>
                </div>

                <form @submit.prevent="submit(tab)" class="space-y-4 max-w-xl">
                    <div v-for="(value, key) in forms[tab].values" :key="key">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            {{ labels[key] || key }}
                        </label>
                        <MediaPicker v-if="imageKeys.includes(key)" v-model="forms[tab].values[key]" />
                        <input
                            v-else
                            v-model="forms[tab].values[key]"
                            type="text"
                            :placeholder="placeholders[key] || ''"
                            class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none"
                        />
                    </div>

                    <button
                        type="submit"
                        :disabled="forms[tab].processing"
                        class="bg-brand-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-brand-700 disabled:opacity-50"
                    >
                        Simpan
                    </button>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
