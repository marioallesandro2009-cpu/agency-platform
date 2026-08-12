<script setup>
import AuthSplitLayout from "@/Layouts/AuthSplitLayout.vue";
import AuthInputField from "@/Components/Auth/AuthInputField.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

const form = useForm({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const submit = () => {
    form.post(route("register"), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <AuthSplitLayout
        reverse
        eyebrow="Manajemen Tim"
        title="Tambah Akun Baru"
        description="Buat akses login baru untuk anggota tim yang akan ikut mengelola konten dan dashboard admin."
        panel-link-text="Kembali ke Dashboard"
        :panel-link-href="route('admin.dashboard')"
    >
        <Head title="Register" />

        <h2 class="text-2xl font-bold text-gray-900 mb-1">Buat Akun Tim</h2>
        <p class="text-sm text-gray-500 mb-8">Isi data di bawah untuk membuat akun admin baru.</p>

        <form @submit.prevent="submit" class="space-y-5 auth-stagger">
            <div class="motion-safe:animate-[fade-in-up_0.5s_ease-out_both]">
                <AuthInputField
                    id="name"
                    v-model="form.name"
                    type="text"
                    label="Nama"
                    icon="user"
                    autocomplete="name"
                    required
                    autofocus
                    :error="form.errors.name"
                />
            </div>

            <div class="motion-safe:animate-[fade-in-up_0.5s_ease-out_both]">
                <AuthInputField
                    id="email"
                    v-model="form.email"
                    type="email"
                    label="Email"
                    icon="mail"
                    autocomplete="username"
                    required
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
                    autocomplete="new-password"
                    required
                    :error="form.errors.password"
                />
            </div>

            <div class="motion-safe:animate-[fade-in-up_0.5s_ease-out_both]">
                <AuthInputField
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    label="Konfirmasi Password"
                    icon="lock"
                    autocomplete="new-password"
                    required
                    :error="form.errors.password_confirmation"
                />
            </div>

            <div class="flex items-center justify-between motion-safe:animate-[fade-in-up_0.5s_ease-out_both]">
                <Link
                    :href="route('admin.dashboard')"
                    class="text-sm font-medium text-brand-600 hover:text-brand-700 hover:underline underline-offset-2"
                >
                    Batal, kembali ke dashboard
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
                {{ form.processing ? "Memproses..." : "Buat Akun" }}
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
.auth-stagger > *:nth-child(5) {
    animation-delay: 0.25s;
}
.auth-stagger > *:nth-child(6) {
    animation-delay: 0.3s;
}
</style>
