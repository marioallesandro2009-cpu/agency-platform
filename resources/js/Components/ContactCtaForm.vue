<script setup>
/**
 * "Talk to Us" style contact CTA — inline SVG illustration + lead-capture form.
 *   - icons: lucide-vue-next <User>/<Phone>/<Mail>/<MessageSquare>
 *   - form: Inertia useForm() posting to leads.store, same field set as
 *     LeadForm.vue
 *   - illustration: original paper-plane design (two flat triangles + a
 *     crease line, brand-green palette, dot decorations) — not a trace of
 *     any third-party asset. Replaced an earlier envelope illustration that
 *     had been adapted from a Freepik vector of uncertain license status;
 *     this one is built from plain geometric shapes with proportions we
 *     picked ourselves, so no attribution is owed.
 *   - animation: #plane float + .dot staggered blink, both authored here
 *     (unchanged in spirit from the earlier version), scoped to this
 *     component only
 *
 * Replaces the plain blue CTA banner previously in Home.vue's "Final CTA"
 * section (page_sections: page=home, section_type=cta) so that section
 * captures a lead directly instead of just linking out to /konsultasi.
 */
import { useForm } from "@inertiajs/vue3";
import { User, Phone, Mail, MessageSquare } from "lucide-vue-next";

defineProps({
    eyebrow: { type: String, default: "" },
    title: { type: String, default: "Siap Membangun Website Impian Anda?" },
    description: { type: String, default: "" },
});

const form = useForm({
    name: "",
    whatsapp: "",
    email: "",
    message: "",
    service_interested: "",
    source: "cta_home",
});

function submit() {
    form.post(route("leads.store"), {
        preserveScroll: true,
    });
}
</script>

<template>
    <section class="contact-cta bg-charcoal py-16 md:py-20 overflow-hidden">
        <div class="max-w-6xl mx-auto px-4">
            <div class="flex flex-col md:flex-row items-center gap-10 md:gap-16">
                <!-- Illustration -->
                <div class="w-full md:w-2/5 flex justify-center flex-shrink-0">
                    <svg viewBox="0 0 360 320" class="w-full max-w-sm" role="img" aria-label="Ilustrasi kirim pesan">
                        <g id="dots" fill="#a3e635">
                            <circle id="dot1" class="dot" cx="60" cy="60" r="6" />
                            <circle id="dot2" class="dot" cx="300" cy="50" r="5" />
                            <circle id="dot3" class="dot" cx="320" cy="140" r="6" />
                            <circle id="dot4" class="dot" cx="30" cy="165" r="5" />
                            <circle id="dot5" class="dot" cx="280" cy="235" r="6" />
                            <circle id="dot6" class="dot" cx="55" cy="245" r="5" />
                        </g>

                        <circle cx="180" cy="175" r="120" fill="#65a30d" opacity="0.12" />

                        <g id="plane">
                            <polygon points="90,205 285,120 195,168" fill="#a3e635" />
                            <polygon points="195,168 285,120 215,235" fill="#4d7c0f" />
                            <line x1="195" y1="168" x2="285" y2="120" stroke="#1a2e05" stroke-width="3" stroke-linecap="round" />
                            <polygon points="90,205 285,120 215,235 195,168" fill="none" stroke="#1a2e05" stroke-width="4" stroke-linejoin="round" stroke-linecap="round" />
                        </g>
                    </svg>
                </div>

                <!-- Form -->
                <div class="w-full md:w-3/5">
                    <p v-if="eyebrow" v-scroll-reveal class="text-brand-400 font-semibold text-sm uppercase tracking-wide mb-2">
                        {{ eyebrow }}
                    </p>
                    <h2 v-scroll-reveal="{ delay: 80 }" class="text-2xl md:text-3xl font-bold text-white mb-3">
                        {{ title }}
                    </h2>
                    <p v-if="description" v-scroll-reveal="{ delay: 120 }" class="text-gray-300 mb-8">
                        {{ description }}
                    </p>

                    <form v-scroll-reveal="{ delay: 160 }" @submit.prevent="submit" class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <div class="field">
                                    <User class="icon" aria-hidden="true" />
                                    <input
                                        v-model="form.name"
                                        type="text"
                                        placeholder="Nama"
                                        required
                                        :aria-invalid="!!form.errors.name"
                                        class="field-input"
                                        :class="form.errors.name ? 'field-input--error' : ''"
                                    />
                                </div>
                                <p v-if="form.errors.name" class="field-error">{{ form.errors.name }}</p>
                            </div>

                            <div>
                                <div class="field">
                                    <Phone class="icon" aria-hidden="true" />
                                    <input
                                        v-model="form.whatsapp"
                                        type="text"
                                        placeholder="Nomor WhatsApp"
                                        required
                                        :aria-invalid="!!form.errors.whatsapp"
                                        class="field-input"
                                        :class="form.errors.whatsapp ? 'field-input--error' : ''"
                                    />
                                </div>
                                <p v-if="form.errors.whatsapp" class="field-error">{{ form.errors.whatsapp }}</p>
                            </div>
                        </div>

                        <div>
                            <div class="field">
                                <Mail class="icon" aria-hidden="true" />
                                <input
                                    v-model="form.email"
                                    type="email"
                                    placeholder="E-mail (opsional)"
                                    :aria-invalid="!!form.errors.email"
                                    class="field-input"
                                    :class="form.errors.email ? 'field-input--error' : ''"
                                />
                            </div>
                            <p v-if="form.errors.email" class="field-error">{{ form.errors.email }}</p>
                        </div>

                        <div>
                            <div class="field field--textarea">
                                <MessageSquare class="icon icon--textarea" aria-hidden="true" />
                                <textarea
                                    v-model="form.message"
                                    rows="3"
                                    placeholder="Pesan (opsional)"
                                    class="field-input"
                                ></textarea>
                            </div>
                        </div>

                        <button type="submit" :disabled="form.processing" class="btn-primary">
                            {{ form.processing ? "Mengirim..." : "Kirim Pesan" }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>

<style scoped>
/* ---- plane float ---- */
#plane {
    animation: plane-float 4s ease-in-out infinite;
    transform-origin: center;
}
@keyframes plane-float {
    0%,
    100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-10px);
    }
}

/* ---- dot blink, staggered ---- */
.dot {
    animation: dot-blink 1.8s ease-in-out infinite;
}
#dot1 {
    animation-delay: 0s;
}
#dot2 {
    animation-delay: 0.3s;
}
#dot3 {
    animation-delay: 0.6s;
}
#dot4 {
    animation-delay: 0.9s;
}
#dot5 {
    animation-delay: 1.2s;
}
#dot6 {
    animation-delay: 1.5s;
}
@keyframes dot-blink {
    0%,
    100% {
        opacity: 0.25;
        transform: scale(0.85);
    }
    50% {
        opacity: 1;
        transform: scale(1.1);
    }
}

@media (prefers-reduced-motion: reduce) {
    #plane,
    .dot {
        animation: none;
    }
}

/* ---- form fields ---- */
.field {
    position: relative;
}
.icon {
    position: absolute;
    left: 0.875rem;
    top: 50%;
    transform: translateY(-50%);
    width: 1.125rem;
    height: 1.125rem;
    color: #9ca3af;
    pointer-events: none;
}
.field--textarea .icon--textarea {
    top: 0.9rem;
    transform: none;
}
.field-input {
    width: 100%;
    padding: 0.625rem 0.875rem 0.625rem 2.5rem;
    border-radius: 0.5rem;
    border: 1px solid rgba(255, 255, 255, 0.12);
    background: rgba(255, 255, 255, 0.05);
    color: #fff;
    font-size: 0.875rem;
}
.field-input::placeholder {
    color: #9ca3af;
}
.field-input:focus {
    outline: none;
    border-color: #84cc16;
    box-shadow: 0 0 0 3px rgba(132, 204, 22, 0.25);
}
.field-input--error {
    border-color: #f87171;
}
.field-error {
    margin-top: 0.25rem;
    font-size: 0.75rem;
    color: #fca5a5;
}

/* ---- submit button: was an orange gradient, now brand green ---- */
.btn-primary {
    width: 100%;
    padding: 0.75rem 1.5rem;
    border-radius: 0.5rem;
    font-weight: 600;
    font-size: 0.9375rem;
    color: #0a0a0a;
    background: linear-gradient(90deg, #a3e635, #4d7c0f);
    background-size: 160% 100%;
    background-position: 0% 0%;
    transition: background-position 0.35s ease, opacity 0.2s ease;
}
.btn-primary:hover:not(:disabled) {
    background-position: 100% 0%;
}
.btn-primary:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

@media (min-width: 640px) {
    .btn-primary {
        width: auto;
    }
}
</style>
