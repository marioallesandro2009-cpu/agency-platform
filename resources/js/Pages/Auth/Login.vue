<script setup>
import Checkbox from "@/Components/Checkbox.vue";
import AuthSplitLayout from "@/Layouts/AuthSplitLayout.vue";
import AuthInputField from "@/Components/Auth/AuthInputField.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <AuthSplitLayout
        eyebrow="Panel Admin"
        title="Selamat Datang Kembali"
        description="Masuk untuk mengelola konten, portfolio, dan permintaan konsultasi dari dashboard admin."
        panel-link-text="Kembali ke Beranda"
        panel-link-href="/"
    >
        <Head title="Log in" />

        <h2 class="text-2xl font-bold text-gray-900 mb-1">Masuk ke Akun</h2>
        <p class="text-sm text-gray-500 mb-8">Masukkan email dan password kamu untuk melanjutkan.</p>

        <Transition
            enter-active-class="transition duration-300 ease-out"
            enter-from-class="opacity-0 -translate-y-1"
            enter-to-class="opacity-100 translate-y-0"
        >
            <div v-if="status" class="mb-6 flex items-center gap-2 rounded-lg bg-green-50 border border-green-200 px-4 py-3 text-sm font-medium text-green-700">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                {{ status }}
            </div>
        </Transition>

        <form @submit.prevent="submit" class="space-y-5 auth-stagger">
            <div class="motion-safe:animate-[fade-in-up_0.5s_ease-out_both]">
                <AuthInputField
                    id="email"
                    v-model="form.email"
                    type="email"
                    label="Email"
                    icon="mail"
                    autocomplete="username"
                    required
                    autofocus
                    :error="form.errors.email"
                />
            </div>

            <div class="motion-safe:animate-[fade-in-up_0.5s_ease-out_both]">
                <AuthInputField
                    id="password"
                    v-model="form.password"
                    type="password"
                    label="Password"
                    icon="lock"
                    autocomplete="current-password"
                    required
                    :error="form.errors.password"
                />
            </div>

            <div class="flex items-center justify-between motion-safe:animate-[fade-in-up_0.5s_ease-out_both]">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="text-sm text-gray-600">Ingat saya</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm font-medium text-brand-600 hover:text-brand-700 hover:underline underline-offset-2"
                >
                    Lupa password?
                </Link>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="motion-safe:animate-[fade-in-up_0.5s_ease-out_both] w-full inline-flex items-center justify-center gap-2 rounded-lg bg-brand-600 text-white font-semibold text-sm py-3 transition-all duration-200 hover:bg-brand-700 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-brand-600/25 active:translate-y-0 active:scale-[0.98] disabled:opacity-60 disabled:pointer-events-none disabled:hover:translate-y-0"
            >
                <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                {{ form.processing ? "Memproses..." : "Masuk" }}
            </button>
        </form>
    </AuthSplitLayout>
</template>

<style scoped>
.auth-stagger > *:nth-child(1) {
    animation-delay: 0.05s;
}
.auth-stagger > *:nth-child(2) {
    animation-delay: 0.1s;
}
.auth-stagger > *:nth-child(3) {
    animation-delay: 0.15s;
}
.auth-stagger > *:nth-child(4) {
    animation-delay: 0.2s;
}
</style>
