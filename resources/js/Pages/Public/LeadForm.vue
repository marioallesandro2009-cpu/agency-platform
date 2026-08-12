<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import PageHeaderScanner from "@/Components/Public/PageHeaderScanner.vue";
import { useForm, usePage } from "@inertiajs/vue3";

const props = defineProps({
    context: String,
    title: String,
    subtitle: String,
    serviceInterested: String,
});

const page = usePage();

const form = useForm({
    name: "",
    whatsapp: "",
    email: "",
    message: "",
    service_interested: props.serviceInterested || "",
    source: props.context,
});

function submit() {
    form.post(route("leads.store"));
}
</script>

<template>
    <SeoHead :title="title" :description="subtitle" />
    <PublicLayout>
        <PageHeaderScanner :title="title" :subtitle="subtitle" />

        <section class="max-w-lg mx-auto px-4 py-16">
            <div v-if="page.props.flash?.error" class="mb-4 bg-red-100 text-red-800 px-4 py-2 rounded text-sm">
                {{ page.props.flash.error }}
            </div>

            <div v-if="context === 'kontak'" class="mb-8 flex flex-wrap gap-3">
                <a
                    v-if="page.props.settings.contact.whatsapp_number"
                    :href="'https://wa.me/' + page.props.settings.contact.whatsapp_number"
                    target="_blank"
                    class="inline-flex items-center gap-2 bg-brand-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-brand-700"
                >
                    Chat WhatsApp
                </a>
                <a
                    v-if="page.props.settings.contact.phone"
                    :href="'tel:' + page.props.settings.contact.phone"
                    class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 text-sm font-medium px-4 py-2 rounded-md hover:bg-gray-50"
                >
                    Telepon
                </a>
                <a
                    v-if="page.props.settings.contact.email"
                    :href="'mailto:' + page.props.settings.contact.email"
                    class="inline-flex items-center gap-2 border border-gray-300 text-gray-700 text-sm font-medium px-4 py-2 rounded-md hover:bg-gray-50"
                >
                    Email
                </a>
            </div>
            <p v-if="context === 'kontak'" class="text-sm text-gray-500 mb-6">
                Atau isi formulir di bawah dan kami akan menghubungi Anda lewat WhatsApp:
            </p>

            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label for="lead-name" class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                    <input
                        id="lead-name"
                        v-model="form.name"
                        type="text"
                        required
                        :aria-invalid="!!form.errors.name"
                        :aria-describedby="form.errors.name ? 'lead-name-error' : undefined"
                        class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none"
                        :class="form.errors.name ? 'border-red-400' : 'border-gray-300'"
                    />
                    <p v-if="form.errors.name" id="lead-name-error" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
                </div>

                <div>
                    <label for="lead-whatsapp" class="block text-sm font-medium text-gray-700 mb-1">Nomor WhatsApp <span class="text-red-500">*</span></label>
                    <input
                        id="lead-whatsapp"
                        v-model="form.whatsapp"
                        type="text"
                        required
                        placeholder="08xxxxxxxxxx"
                        :aria-invalid="!!form.errors.whatsapp"
                        :aria-describedby="form.errors.whatsapp ? 'lead-whatsapp-error' : undefined"
                        class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none"
                        :class="form.errors.whatsapp ? 'border-red-400' : 'border-gray-300'"
                    />
                    <p v-if="form.errors.whatsapp" id="lead-whatsapp-error" class="text-xs text-red-500 mt-1">{{ form.errors.whatsapp }}</p>
                </div>

                <div>
                    <label for="lead-email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input
                        id="lead-email"
                        v-model="form.email"
                        type="email"
                        :aria-invalid="!!form.errors.email"
                        :aria-describedby="form.errors.email ? 'lead-email-error' : undefined"
                        class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none"
                        :class="form.errors.email ? 'border-red-400' : 'border-gray-300'"
                    />
                    <p v-if="form.errors.email" id="lead-email-error" class="text-xs text-red-500 mt-1">{{ form.errors.email }}</p>
                </div>

                <div v-if="context === 'konsultasi'">
                    <label for="lead-service" class="block text-sm font-medium text-gray-700 mb-1">Layanan yang Diminati</label>
                    <input
                        id="lead-service"
                        v-model="form.service_interested"
                        type="text"
                        placeholder="mis. Website UMKM"
                        class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none border-gray-300"
                    />
                </div>

                <div>
                    <label for="lead-message" class="block text-sm font-medium text-gray-700 mb-1">Pesan</label>
                    <textarea
                        id="lead-message"
                        v-model="form.message"
                        rows="4"
                        placeholder="Ceritakan kebutuhan Anda..."
                        class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none border-gray-300"
                    ></textarea>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full bg-brand-600 text-white font-medium px-6 py-3 rounded-md hover:bg-brand-700 disabled:opacity-50"
                >
                    {{ form.processing ? "Mengirim..." : "Kirim & Lanjut ke WhatsApp" }}
                </button>
                <p class="text-xs text-gray-500 text-center">
                    Setelah mengirim, Anda akan diarahkan ke WhatsApp untuk melanjutkan percakapan.
                </p>
            </form>
        </section>
    </PublicLayout>
</template>
