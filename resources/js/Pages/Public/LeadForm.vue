<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import PageHeaderScanner from "@/Components/Public/PageHeaderScanner.vue";
import StepIndicator from "@/Components/Public/StepIndicator.vue";
import Button from "@/Components/Public/Button.vue";
import { useForm, usePage } from "@inertiajs/vue3";
import { CircleCheck } from "lucide-vue-next";
import { computed, ref } from "vue";

const props = defineProps({
    context: String,
    title: String,
    subtitle: String,
    serviceInterested: String,
    selectedPackage: Object,
});

const page = usePage();

const form = useForm({
    name: "",
    email: "",
    whatsapp: "",
    service_interested: props.serviceInterested || "",
    budget_estimate: "",
    address: "",
    message: "",
    pricing_category: props.selectedPackage?.category || "",
    pricing_tier: props.selectedPackage?.tier || "",
    source: props.context,
});

const STEPS = ["Data Diri", "Kebutuhan", "Detail", "Ringkasan"];
const currentStep = ref(1);
const stepError = ref("");
const whatsappPattern = /^[0-9+\-\s()]{8,30}$/;

// Driven by the flash message rather than a local "submitted" ref: the
// successful POST redirects back (Inertia follows it as a fresh page
// visit), which remounts this component — a local ref set from an
// onSuccess callback would belong to the old, already-discarded instance.
const submitted = computed(() => !!page.props.flash?.success);

const budgetOptions = [
    { value: "", label: "Belum tahu / masih survei" },
    { value: "< Rp1 Juta", label: "< Rp1 Juta" },
    { value: "Rp1 - 3 Juta", label: "Rp1 - 3 Juta" },
    { value: "Rp3 - 7 Juta", label: "Rp3 - 7 Juta" },
    { value: "Rp7 - 15 Juta", label: "Rp7 - 15 Juta" },
    { value: "> Rp15 Juta", label: "> Rp15 Juta" },
];

const canProceed = computed(() => {
    if (currentStep.value === 1) {
        return form.name.trim().length > 0 && whatsappPattern.test(form.whatsapp.trim());
    }
    if (currentStep.value === 2) {
        return form.service_interested.trim().length > 0;
    }
    return true;
});

function next() {
    if (!canProceed.value) {
        stepError.value =
            currentStep.value === 1
                ? "Lengkapi Nama Perusahaan dan Nomor WA yang valid terlebih dahulu."
                : "Pilih Jenis Layanan terlebih dahulu.";
        return;
    }
    stepError.value = "";
    currentStep.value = Math.min(currentStep.value + 1, STEPS.length);
}

function back() {
    stepError.value = "";
    currentStep.value = Math.max(currentStep.value - 1, 1);
}

function editStep(step) {
    stepError.value = "";
    currentStep.value = step;
}

function submit() {
    form.post(route("leads.store"));
}

function formatPrice(price) {
    return "Rp " + new Intl.NumberFormat("id-ID").format(price);
}
</script>

<template>
    <SeoHead :title="title" :description="subtitle" />
    <PublicLayout>
        <PageHeaderScanner :title="title" :subtitle="subtitle" />

        <section class="max-w-3xl mx-auto px-4 py-16 md:py-20">
            <!-- Thank-you state -->
            <div v-if="submitted" class="text-center py-16">
                <div class="w-16 h-16 rounded-full bg-brand-100 text-brand-600 flex items-center justify-center mx-auto mb-5">
                    <CircleCheck class="w-9 h-9" />
                </div>
                <h2 class="text-xl font-bold text-gray-900 mb-2">Terima Kasih!</h2>
                <p class="text-gray-500 max-w-sm mx-auto">{{ page.props.flash.success }}</p>
            </div>

            <template v-else>
                <div v-if="context === 'kontak'" class="mb-8 flex flex-wrap gap-3">
                    <a
                        v-if="page.props.settings.contact.whatsapp_number"
                        :href="'https://wa.me/' + page.props.settings.contact.whatsapp_number + '?text=Halo%2C%20saya%20mau%20nanya-nanya'"
                        target="_blank"
                        rel="noopener"
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

                <div class="grid grid-cols-1 md:grid-cols-[180px_1fr] gap-6 md:gap-10">
                    <StepIndicator :steps="STEPS" :current-step="currentStep" />

                    <form @submit.prevent="submit">
                        <!-- Step 1: Data Diri -->
                        <div v-if="currentStep === 1" class="space-y-4">
                            <h2 class="font-bold tracking-tight text-lg text-gray-900">Data Diri</h2>
                            <div>
                                <label for="lead-name" class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan <span class="text-red-500">*</span></label>
                                <input
                                    id="lead-name"
                                    v-model="form.name"
                                    type="text"
                                    required
                                    :aria-invalid="!!form.errors.name"
                                    class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none"
                                    :class="form.errors.name ? 'border-red-400' : 'border-gray-300'"
                                />
                                <p v-if="form.errors.name" class="text-xs text-red-500 mt-1">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <label for="lead-email" class="block text-sm font-medium text-gray-700 mb-1">Email (opsional)</label>
                                <input
                                    id="lead-email"
                                    v-model="form.email"
                                    type="email"
                                    :aria-invalid="!!form.errors.email"
                                    class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none"
                                    :class="form.errors.email ? 'border-red-400' : 'border-gray-300'"
                                />
                                <p v-if="form.errors.email" class="text-xs text-red-500 mt-1">{{ form.errors.email }}</p>
                            </div>

                            <div>
                                <label for="lead-whatsapp" class="block text-sm font-medium text-gray-700 mb-1">Nomor WA <span class="text-red-500">*</span></label>
                                <input
                                    id="lead-whatsapp"
                                    v-model="form.whatsapp"
                                    type="text"
                                    required
                                    placeholder="08xxxxxxxxxx"
                                    :aria-invalid="!!form.errors.whatsapp"
                                    class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none"
                                    :class="form.errors.whatsapp ? 'border-red-400' : 'border-gray-300'"
                                />
                                <p v-if="form.errors.whatsapp" class="text-xs text-red-500 mt-1">{{ form.errors.whatsapp }}</p>
                            </div>
                        </div>

                        <!-- Step 2: Kebutuhan -->
                        <div v-if="currentStep === 2" class="space-y-4">
                            <h2 class="font-bold tracking-tight text-lg text-gray-900">Kebutuhan</h2>
                            <div>
                                <label for="lead-service" class="block text-sm font-medium text-gray-700 mb-1">Jenis Layanan <span class="text-red-500">*</span></label>
                                <input
                                    id="lead-service"
                                    v-model="form.service_interested"
                                    type="text"
                                    required
                                    placeholder="mis. Website Company Profile"
                                    class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none border-gray-300"
                                />
                            </div>
                            <div>
                                <label for="lead-budget" class="block text-sm font-medium text-gray-700 mb-1">Estimasi Anggaran</label>
                                <select
                                    id="lead-budget"
                                    v-model="form.budget_estimate"
                                    class="w-full border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none border-gray-300"
                                >
                                    <option v-for="opt in budgetOptions" :key="opt.value" :value="opt.value">{{ opt.label }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Step 3: Detail -->
                        <div v-if="currentStep === 3" class="space-y-4">
                            <h2 class="font-bold tracking-tight text-lg text-gray-900">Detail</h2>
                            <div>
                                <label for="lead-address" class="block text-sm font-medium text-gray-700 mb-1">Detail Alamat (opsional — boleh dilewati)</label>
                                <textarea
                                    id="lead-address"
                                    v-model="form.address"
                                    rows="2"
                                    placeholder="Alamat kantor/usaha Anda..."
                                    class="w-full resize-y border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none border-gray-300"
                                ></textarea>
                            </div>
                            <div>
                                <label for="lead-message" class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Kebutuhan (opsional)</label>
                                <textarea
                                    id="lead-message"
                                    v-model="form.message"
                                    rows="4"
                                    placeholder="Ceritakan kebutuhan Anda..."
                                    class="w-full resize-y border rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-brand-500 focus:outline-none border-gray-300"
                                ></textarea>
                            </div>
                        </div>

                        <!-- Step 4: Ringkasan & Kirim -->
                        <div v-if="currentStep === 4" class="space-y-6">
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <h2 class="font-bold tracking-tight text-lg text-gray-900">Data Kamu</h2>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4 space-y-3 text-sm">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-gray-500">Nama Perusahaan / Email / WA</p>
                                            <p class="text-gray-900">{{ form.name }}<span v-if="form.email"> - {{ form.email }}</span> - {{ form.whatsapp }}</p>
                                        </div>
                                        <button type="button" @click="editStep(1)" class="text-brand-600 hover:underline flex-shrink-0">Edit</button>
                                    </div>
                                    <div class="flex items-start justify-between gap-3 pt-3 border-t border-gray-200">
                                        <div>
                                            <p class="text-gray-500">Jenis Layanan / Estimasi Anggaran</p>
                                            <p class="text-gray-900">{{ form.service_interested }}<span v-if="form.budget_estimate"> - {{ form.budget_estimate }}</span></p>
                                        </div>
                                        <button type="button" @click="editStep(2)" class="text-brand-600 hover:underline flex-shrink-0">Edit</button>
                                    </div>
                                    <div class="flex items-start justify-between gap-3 pt-3 border-t border-gray-200">
                                        <div>
                                            <p class="text-gray-500">Alamat / Deskripsi</p>
                                            <p class="text-gray-900">
                                                <span v-if="form.address">{{ form.address }}</span>
                                                <span v-if="form.address && form.message"> - </span>
                                                <span v-if="form.message">{{ form.message }}</span>
                                                <span v-if="!form.address && !form.message" class="text-gray-500">- Tidak diisi -</span>
                                            </p>
                                        </div>
                                        <button type="button" @click="editStep(3)" class="text-brand-600 hover:underline flex-shrink-0">Edit</button>
                                    </div>
                                </div>
                            </div>

                            <div v-if="selectedPackage">
                                <h2 class="font-bold tracking-tight text-lg text-gray-900 mb-2">Paket yang Kamu Pilih</h2>
                                <div class="border border-brand-200 bg-brand-50 rounded-lg p-4">
                                    <p class="font-bold tracking-tight text-lg text-gray-900">{{ selectedPackage.name }}</p>
                                    <p class="text-lg font-bold text-gray-900 mb-2">
                                        {{ selectedPackage.price ? formatPrice(selectedPackage.price) : selectedPackage.price_suffix }}
                                    </p>
                                    <ul class="space-y-1.5">
                                        <li
                                            v-for="feature in (selectedPackage.features || []).slice(0, 4)"
                                            :key="feature"
                                            class="flex items-start gap-2 text-sm text-gray-600"
                                        >
                                            <CircleCheck class="w-4 h-4 text-brand-600 shrink-0 mt-0.5" />
                                            <span>{{ feature }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <p v-if="stepError" class="text-xs text-red-500 mt-3">{{ stepError }}</p>

                        <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
                            <button
                                v-if="currentStep > 1"
                                type="button"
                                @click="back"
                                class="text-sm font-medium text-gray-600 hover:text-gray-900 px-4 py-2"
                            >
                                &larr; Kembali
                            </button>
                            <span v-else></span>

                            <Button v-if="currentStep < STEPS.length" type="button" @click="next">
                                Lanjut &rarr;
                            </Button>
                            <Button v-else type="submit" :disabled="form.processing">
                                {{ form.processing ? "Mengirim..." : "Kirim" }}
                            </Button>
                        </div>
                    </form>
                </div>
            </template>
        </section>
    </PublicLayout>
</template>
