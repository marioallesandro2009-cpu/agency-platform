<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import MediaPicker from "@/Components/MediaPicker.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    settings: Object,
});

const page = usePage();
const tabs = ["general", "contact", "social", "branding", "integrations"];
const activeTab = ref("general");
const imageKeys = ["logo", "logo_dark", "favicon", "og_image"];
const placeholders = {
    ga_measurement_id: "G-XXXXXXXXXX",
};

const labels = {
    website_name: "Nama Website",
    tagline: "Tagline",
    description: "Deskripsi",
    logo: "Logo",
    logo_dark: "Logo Dark Mode",
    favicon: "Favicon",
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
};

const forms = {};
tabs.forEach((tab) => {
    forms[tab] = useForm({
        group: tab,
        values: { ...props.settings[tab] },
    });
});

const submit = (tab) => {
    forms[tab].put(route("admin.settings.update"), {
        preserveScroll: true,
    });
};
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