<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import { router, usePage } from "@inertiajs/vue3";
import { confirmDialog } from "@/composables/useConfirmDialog";

const props = defineProps({
    stats: Object,
    visitorsChart: Array,
});

const page = usePage();

async function resetVisitors() {
    const ok = await confirmDialog({
        title: "Reset Pengunjung",
        message: "Reset semua data pengunjung? Riwayat traffic yang sudah tercatat akan dihapus dan mulai dihitung dari 0 lagi. Aksi ini tidak bisa dibatalkan.",
        confirmLabel: "Reset",
        danger: true,
    });
    if (ok) {
        router.delete(route("admin.visitors.reset"), { preserveScroll: true });
    }
}

const CHART_HEIGHT = 112; // px
const maxVisits = Math.max(1, ...props.visitorsChart.map((d) => d.total));

const barHeight = (total) => Math.max(4, (total / maxVisits) * CHART_HEIGHT) + "px";

const formatDate = (dateStr) => {
    // dateStr format: "2026-08-09" or ISO datetime, take MM-DD
    const datePart = dateStr.split("T")[0];
    return datePart.slice(5);
};
</script>

<template>
    <AdminLayout>
        <template #header>Dashboard</template>

        <p class="text-sm text-gray-500 mb-4">
            Website: <strong>{{ page.props.settings.general.website_name }}</strong>
        </p>

        <div v-if="page.props.flash?.success" class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded">
            {{ page.props.flash.success }}
        </div>

        <div class="flex items-center justify-between mb-2">
            <p class="text-sm font-semibold text-gray-700">Statistik Pengunjung</p>
            <button
                type="button"
                @click="resetVisitors"
                class="text-xs font-medium text-red-600 border border-red-200 px-3 py-1.5 rounded-md hover:bg-red-50"
            >
                Reset Pengunjung
            </button>
        </div>

        <!-- Visitor cards -->
        <div class="grid grid-cols-2 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-2xl shadow p-5">
                <p class="text-sm text-gray-500">Pengunjung Hari Ini</p>
                <p class="text-3xl font-bold text-brand-600 mt-1">{{ stats.visitorsToday }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow p-5">
                <p class="text-sm text-gray-500">Total Pengunjung</p>
                <p class="text-3xl font-bold text-gray-800 mt-1">{{ stats.visitorsTotal }}</p>
            </div>
        </div>

        <!-- Simple 7-day traffic bar chart -->
        <div class="bg-white rounded-2xl shadow p-5 mb-6">
            <p class="text-sm font-semibold text-gray-700 mb-4">Traffic 7 Hari Terakhir</p>
            <div v-if="visitorsChart.length === 0" class="text-sm text-gray-400 text-center py-8">
                Belum ada data
            </div>
            <div v-else class="flex items-end gap-3" :style="{ height: CHART_HEIGHT + 'px' }">
                <div
                    v-for="day in visitorsChart"
                    :key="day.visit_date"
                    class="flex-1 flex flex-col items-center justify-end h-full gap-1"
                >
                    <div
                        class="w-full bg-brand-500 rounded-t-md"
                        :style="{ height: barHeight(day.total) }"
                    ></div>
                </div>
            </div>
            <div v-if="visitorsChart.length > 0" class="flex gap-3 mt-1">
                <div v-for="day in visitorsChart" :key="day.visit_date" class="flex-1 text-center">
                    <span class="text-[10px] text-gray-400">{{ formatDate(day.visit_date) }}</span>
                </div>
            </div>
        </div>

        <!-- Business stats -->
        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-500">Leads</p>
                <p class="text-2xl font-bold text-gray-800">{{ stats.leads }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-500">Portfolio</p>
                <p class="text-2xl font-bold text-gray-800">{{ stats.portfolio }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-500">Services</p>
                <p class="text-2xl font-bold text-gray-800">{{ stats.services }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-500">Blog Published</p>
                <p class="text-2xl font-bold text-gray-800">{{ stats.blogPublished }}</p>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-500">Blog Draft</p>
                <p class="text-2xl font-bold text-gray-800">{{ stats.blogDraft }}</p>
            </div>
        </div>
    </AdminLayout>
</template>