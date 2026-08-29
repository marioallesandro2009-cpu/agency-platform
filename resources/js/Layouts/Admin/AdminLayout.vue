<script setup>
import { ref } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import AdminIcon from "@/Components/Admin/AdminIcon.vue";
import ConfirmDialog from "@/Components/ConfirmDialog.vue";
const page = usePage();
const sidebarOpen = ref(true);
const userMenuOpen = ref(false);

function closeUserMenuIfFocusLeft(event) {
    if (!event.currentTarget.contains(event.relatedTarget)) {
        userMenuOpen.value = false;
    }
}

function logout() {
    router.post(route("logout"));
}

const menu = [
    { label: "Dashboard", route: "admin.dashboard", icon: "dashboard" },
    // Below mirrors the public navbar order (Home, Jasa & Layanan, Harga,
    // Portfolio, Blog, Tentang Kami, Kontak) as closely as the admin's
    // resource set allows; items with no direct public-navbar counterpart
    // are kept together at the end, in their previous relative order.
    { label: "Homepage Sections", route: "admin.page-sections.index", icon: "sections" }, // Home
    { label: "Layanan", route: "admin.services.index", icon: "services" }, // Jasa & Layanan
    { label: "Pricing", route: "admin.pricing.index", icon: "pricing" }, // Harga
    { label: "Maintenance", route: "admin.maintenance-plans.index", icon: "maintenance" },
    { label: "Portfolio", route: "admin.portfolio.index", icon: "portfolio" },
    { label: "Blog", route: "admin.blog.index", icon: "blog" },
    { label: "Tentang Kami", route: "admin.about.edit", icon: "about" },
    { label: "Tech Stack", route: "admin.tech-stacks.index", icon: "techstack" },
    { label: "Leads", route: "admin.leads.index", icon: "leads" }, // Kontak
    { label: "Testimonials", route: "admin.testimonials.index", icon: "testimonials" },
    { label: "FAQ", route: "admin.faqs.index", icon: "faq" },
    { label: "Media", route: "admin.media.index", icon: "media" },
    { label: "Maskot", route: "admin.mascot.index", icon: "mascot" },
    { label: "Navigasi", route: "admin.navigation.index", icon: "navigation" },
    { label: "Settings", route: "admin.settings.index", icon: "settings" },
];
</script>
<template>
    <div class="min-h-screen bg-gray-100 flex">
        <aside
            :class="sidebarOpen ? 'w-64' : 'w-16'"
            class="bg-charcoal text-white flex-shrink-0 transition-all duration-200 flex flex-col"
        >
            <div class="h-16 flex items-center justify-between px-4 border-b border-white/10">
                <span v-if="sidebarOpen" class="font-bold text-lg truncate">Admin Panel</span>
                <button @click="sidebarOpen = !sidebarOpen" class="text-gray-300 hover:text-white p-1" aria-label="Ciutkan sidebar">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
            <nav class="flex-1 py-4 space-y-0.5 px-2">
                <Link
                    v-for="item in menu"
                    :key="item.route"
                    :href="route(item.route)"
                    :title="sidebarOpen ? '' : item.label"
                    :class="route().current(item.route)
                        ? 'bg-brand-600 text-white'
                        : 'text-gray-300 hover:bg-white/10 hover:text-white'"
                    class="flex items-center gap-3 px-3 py-2 text-sm rounded-md transition-colors"
                >
                    <AdminIcon :name="item.icon" />
                    <span v-if="sidebarOpen" class="truncate">{{ item.label }}</span>
                </Link>
            </nav>
            <div class="p-4 border-t border-white/10 text-xs text-gray-400 truncate">
                <span v-if="sidebarOpen">{{ page.props.auth.user.name }}</span>
            </div>
        </aside>
        <div class="flex-1 flex flex-col min-w-0">
            <header class="h-16 bg-white border-b flex items-center justify-between px-6">
                <h1 class="text-lg font-semibold text-gray-800">
                    <slot name="header">Dashboard</slot>
                </h1>
                <div class="relative" @focusout="closeUserMenuIfFocusLeft">
                    <button
                        @click="userMenuOpen = !userMenuOpen"
                        :aria-expanded="userMenuOpen"
                        aria-controls="admin-user-menu"
                        class="flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900"
                    >
                        <span class="w-7 h-7 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center text-xs font-semibold flex-shrink-0">
                            {{ page.props.auth.user.name.charAt(0).toUpperCase() }}
                        </span>
                        {{ page.props.auth.user.name }}
                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div
                        v-if="userMenuOpen"
                        id="admin-user-menu"
                        class="absolute right-0 top-full mt-1 w-40 bg-white shadow-lg rounded-md border border-gray-100 py-1 z-10"
                    >
                        <Link :href="route('profile.edit')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profil</Link>
                        <button @click="logout" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-50">Logout</button>
                    </div>
                </div>
            </header>
            <main class="flex-1 p-6 overflow-auto">
                <slot />
            </main>
        </div>
        <ConfirmDialog />
    </div>
</template>