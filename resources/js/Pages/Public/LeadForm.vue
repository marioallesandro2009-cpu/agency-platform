<script setup>
import PublicLayout from "@/Layouts/Public/PublicLayout.vue";
import SeoHead from "@/Components/SeoHead.vue";
import SpacePageHeader from "@/Components/Public/SpacePageHeader.vue";
import StepIndicator from "@/Components/Public/StepIndicator.vue";
import Button from "@/Components/Public/Button.vue";
import { useForm, usePage } from "@inertiajs/vue3";
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

// Shared dark-field styling for every input on this form.
const fieldClass =
    "w-full rounded-xl border bg-white/[0.04] px-4 py-3 text-sm text-white placeholder:text-white/30 " +
    "focus:outline-none focus:border-brand-400 focus:ring-2 focus:ring-brand-400/30";
const labelClass = "block font-mono text-[10px] uppercase tracking-[0.2em] text-white/55 mb-2";

const pad = (n) => String(n).padStart(2, "0");

function formatPrice(price) {
    return "Rp " + new Intl.NumberFormat("id-ID").format(price);
}
</script>

<template>
    <SeoHead :title="title" :description="subtitle" />
    <PublicLayout theme="space">
        <SpacePageHeader eyebrow="Konsultasi" code="CTA" :title="title" :subtitle="subtitle" />

        <section class="max-w-[1440px] mx-auto px-4 md:px-8 py-16 md:py-24">
            <!-- Thank-you state -->
            <div v-if="submitted" class="relative overflow-hidden rounded-3xl border border-brand-400/30 bg-space-deep text-center px-6 py-20 max-w-3xl mx-auto">
                <div class="absolute inset-0 bg-glow-drift pointer-events-none" aria-hidden="true"></div>
                <div class="relative w-16 h-16 rounded-full border border-brand-400 text-brand-400 text-2xl flex items-center justify-center mx-auto mb-6 shadow-[0_0_40px_-6px_rgb(var(--lime-400)/0.6)]" aria-hidden="true">&#10022;</div>
                <p class="relative font-mono text-[10px] uppercase tracking-[0.25em] text-brand-400 mb-3">Transmisi diterima</p>
                <h2 class="relative font-display font-semibold text-3xl text-white tracking-tight mb-3">Terima Kasih!</h2>
                <p class="relative text-white/60 max-w-sm mx-auto">{{ page.props.flash.success }}</p>
            </div>

            <div v-else class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-16">
                <!-- Left: direct contact + step rail -->
                <aside class="lg:col-span-4">
                    <div class="lg:sticky lg:top-28 space-y-10">
                        <div v-if="context === 'kontak'">
                            <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-white/45 mb-4">Hubungi langsung</p>
                            <div class="flex flex-wrap gap-2">
                                <a
                                    v-if="page.props.settings.contact.whatsapp_number"
                                    :href="'https://wa.me/' + page.props.settings.contact.whatsapp_number + '?text=Halo%2C%20saya%20mau%20nanya-nanya'"
                                    target="_blank"
                                    rel="noopener"
                                    class="btn-space btn-space--pill"
                                >
                                    Chat WhatsApp <span class="btn-space__arrow" aria-hidden="true">&rarr;</span>
                                </a>
                                <a v-if="page.props.settings.contact.phone" :href="'tel:' + page.props.settings.contact.phone" class="btn-space btn-space--glass">
                                    Telepon <span class="btn-space__arrow" aria-hidden="true">&rarr;</span>
                                </a>
                                <a v-if="page.props.settings.contact.email" :href="'mailto:' + page.props.settings.contact.email" class="btn-space btn-space--glass">
                                    Email <span class="btn-space__arrow" aria-hidden="true">&rarr;</span>
                                </a>
                            </div>
                            <p class="text-sm text-white/45 mt-5">Atau isi formulir dan kami akan menghubungi Anda lewat WhatsApp.</p>
                        </div>

                        <StepIndicator :steps="STEPS" :current-step="currentStep" />
                    </div>
                </aside>

                <!-- Right: the wizard -->
                <form @submit.prevent="submit" class="lg:col-span-8 rounded-2xl border border-white/10 bg-space-deep/60 backdrop-blur-md p-6 md:p-10">
                    <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-white/40 mb-2">
                        Langkah <span class="text-brand-400">{{ pad(currentStep) }}</span> / {{ pad(STEPS.length) }}
                    </p>

                    <!-- Step 1: Data Diri -->
                    <div v-if="currentStep === 1" class="space-y-5">
                        <h2 class="font-display font-semibold text-2xl md:text-3xl text-white tracking-tight mb-6">Data Diri</h2>
                        <div>
                            <label for="lead-name" :class="labelClass">Nama Perusahaan <span class="text-brand-400">*</span></label>
                            <input
                                id="lead-name"
                                v-model="form.name"
                                type="text"
                                required
                                :aria-invalid="!!form.errors.name"
                                :class="[fieldClass, form.errors.name ? 'border-red-400' : 'border-white/15']"
                            />
                            <p v-if="form.errors.name" class="text-xs text-red-300 mt-1.5">{{ form.errors.name }}</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label for="lead-whatsapp" :class="labelClass">Nomor WA <span class="text-brand-400">*</span></label>
                                <input
                                    id="lead-whatsapp"
                                    v-model="form.whatsapp"
                                    type="text"
                                    required
                                    placeholder="08xxxxxxxxxx"
                                    :aria-invalid="!!form.errors.whatsapp"
                                    :class="[fieldClass, form.errors.whatsapp ? 'border-red-400' : 'border-white/15']"
                                />
                                <p v-if="form.errors.whatsapp" class="text-xs text-red-300 mt-1.5">{{ form.errors.whatsapp }}</p>
                            </div>
                            <div>
                                <label for="lead-email" :class="labelClass">Email (opsional)</label>
                                <input
                                    id="lead-email"
                                    v-model="form.email"
                                    type="email"
                                    :aria-invalid="!!form.errors.email"
                                    :class="[fieldClass, form.errors.email ? 'border-red-400' : 'border-white/15']"
                                />
                                <p v-if="form.errors.email" class="text-xs text-red-300 mt-1.5">{{ form.errors.email }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Kebutuhan -->
                    <div v-if="currentStep === 2" class="space-y-5">
                        <h2 class="font-display font-semibold text-2xl md:text-3xl text-white tracking-tight mb-6">Kebutuhan</h2>
                        <div>
                            <label for="lead-service" :class="labelClass">Jenis Layanan <span class="text-brand-400">*</span></label>
                            <input
                                id="lead-service"
                                v-model="form.service_interested"
                                type="text"
                                required
                                placeholder="mis. Website Company Profile"
                                :class="[fieldClass, 'border-white/15']"
                            />
                        </div>
                        <fieldset>
                            <legend :class="labelClass">Estimasi Anggaran</legend>
                            <div class="flex flex-wrap gap-2">
                                <button
                                    v-for="opt in budgetOptions"
                                    :key="opt.value"
                                    type="button"
                                    class="space-tab px-4 py-2 rounded-full text-sm"
                                    :aria-pressed="form.budget_estimate === opt.value"
                                    @click="form.budget_estimate = opt.value"
                                >
                                    {{ opt.label }}
                                </button>
                            </div>
                        </fieldset>
                    </div>

                    <!-- Step 3: Detail -->
                    <div v-if="currentStep === 3" class="space-y-5">
                        <h2 class="font-display font-semibold text-2xl md:text-3xl text-white tracking-tight mb-6">Detail</h2>
                        <div>
                            <label for="lead-address" :class="labelClass">Detail Alamat (opsional, boleh dilewati)</label>
                            <textarea
                                id="lead-address"
                                v-model="form.address"
                                rows="2"
                                placeholder="Alamat kantor/usaha Anda..."
                                :class="[fieldClass, 'border-white/15 resize-y']"
                            ></textarea>
                        </div>
                        <div>
                            <label for="lead-message" :class="labelClass">Deskripsi Kebutuhan (opsional)</label>
                            <textarea
                                id="lead-message"
                                v-model="form.message"
                                rows="5"
                                placeholder="Ceritakan kebutuhan Anda..."
                                :class="[fieldClass, 'border-white/15 resize-y']"
                            ></textarea>
                        </div>
                    </div>

                    <!-- Step 4: Ringkasan & Kirim -->
                    <div v-if="currentStep === 4" class="space-y-8">
                        <div>
                            <h2 class="font-display font-semibold text-2xl md:text-3xl text-white tracking-tight mb-6">Ringkasan</h2>
                            <dl class="rounded-xl border border-white/10 divide-y divide-white/10 text-sm">
                                <div class="flex items-start justify-between gap-4 p-5">
                                    <div class="min-w-0">
                                        <dt class="font-mono text-[10px] uppercase tracking-[0.2em] text-white/40 mb-1.5">Nama / Email / WA</dt>
                                        <dd class="text-white/85 break-words">{{ form.name }}<span v-if="form.email"> · {{ form.email }}</span> · {{ form.whatsapp }}</dd>
                                    </div>
                                    <button type="button" @click="editStep(1)" class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-400 hover:text-brand-300 shrink-0">Edit</button>
                                </div>
                                <div class="flex items-start justify-between gap-4 p-5">
                                    <div class="min-w-0">
                                        <dt class="font-mono text-[10px] uppercase tracking-[0.2em] text-white/40 mb-1.5">Layanan / Anggaran</dt>
                                        <dd class="text-white/85 break-words">{{ form.service_interested }}<span v-if="form.budget_estimate"> · {{ form.budget_estimate }}</span></dd>
                                    </div>
                                    <button type="button" @click="editStep(2)" class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-400 hover:text-brand-300 shrink-0">Edit</button>
                                </div>
                                <div class="flex items-start justify-between gap-4 p-5">
                                    <div class="min-w-0">
                                        <dt class="font-mono text-[10px] uppercase tracking-[0.2em] text-white/40 mb-1.5">Alamat / Deskripsi</dt>
                                        <dd class="text-white/85 break-words">
                                            <span v-if="form.address">{{ form.address }}</span>
                                            <span v-if="form.address && form.message"> · </span>
                                            <span v-if="form.message">{{ form.message }}</span>
                                            <span v-if="!form.address && !form.message" class="text-white/40">Tidak diisi</span>
                                        </dd>
                                    </div>
                                    <button type="button" @click="editStep(3)" class="font-mono text-[10px] uppercase tracking-[0.2em] text-brand-400 hover:text-brand-300 shrink-0">Edit</button>
                                </div>
                            </dl>
                        </div>

                        <div v-if="selectedPackage">
                            <p class="font-mono text-[10px] uppercase tracking-[0.25em] text-white/45 mb-3">Paket yang kamu pilih</p>
                            <div class="relative overflow-hidden rounded-xl border border-brand-400/40 bg-brand-400/[0.05] p-6">
                                <div class="flex flex-wrap items-baseline justify-between gap-3 mb-4">
                                    <p class="font-display font-semibold text-xl text-white">{{ selectedPackage.name }}</p>
                                    <p class="font-display text-xl text-brand-300 tabular-nums">
                                        {{ selectedPackage.price ? formatPrice(selectedPackage.price) : selectedPackage.price_suffix }}
                                    </p>
                                </div>
                                <ul class="space-y-2">
                                    <li
                                        v-for="(feature, i) in (selectedPackage.features || []).slice(0, 4)"
                                        :key="i"
                                        class="flex items-start gap-3 text-sm text-white/70"
                                    >
                                        <span class="w-3 h-px bg-brand-400 shrink-0 mt-[0.7rem]" aria-hidden="true"></span>
                                        <!-- Package features are {label, note} objects (older rows may be plain strings). -->
                                        <span>{{ feature?.label ?? feature }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <p v-if="stepError" class="text-xs text-red-300 mt-4" role="alert">{{ stepError }}</p>

                    <div class="flex items-center justify-between mt-10 pt-6 border-t border-white/10">
                        <button
                            v-if="currentStep > 1"
                            type="button"
                            @click="back"
                            class="font-mono text-[11px] uppercase tracking-[0.18em] text-white/55 hover:text-white px-2 py-2"
                        >
                            &larr; Kembali
                        </button>
                        <span v-else></span>

                        <Button v-if="currentStep < STEPS.length" type="button" variant="pill" size="large" @click="next">
                            Lanjut
                        </Button>
                        <Button v-else type="submit" variant="pill" size="large" :disabled="form.processing">
                            {{ form.processing ? "Mengirim..." : "Kirim" }}
                        </Button>
                    </div>
                </form>
            </div>
        </section>
    </PublicLayout>
</template>

