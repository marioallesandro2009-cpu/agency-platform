<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import EmptyState from "@/Components/Admin/EmptyState.vue";
import { router, usePage } from "@inertiajs/vue3";
import { reactive } from "vue";
import { confirmDialog } from "@/composables/useConfirmDialog";

const props = defineProps({
    leads: Array,
    filters: Object,
    counts: Object,
});

const page = usePage();

const statusLabel = {
    new: "Baru",
    contacted: "Dihubungi",
    in_progress: "Proses",
    won: "Deal",
    lost: "Batal",
};
const statusColor = {
    new: "bg-brand-100 text-brand-700",
    contacted: "bg-yellow-100 text-yellow-700",
    in_progress: "bg-purple-100 text-purple-700",
    won: "bg-green-100 text-green-700",
    lost: "bg-gray-100 text-gray-500",
};
const statusOptions = Object.keys(statusLabel);

const notesDraft = reactive({});

function filterByStatus(status) {
    router.get(route("admin.leads.index"), status ? { status } : {}, { preserveState: true });
}

function updateStatus(lead, status) {
    router.patch(route("admin.leads.update-status", lead.id), { status, admin_notes: lead.admin_notes }, { preserveScroll: true });
}

function saveNotes(lead) {
    router.patch(
        route("admin.leads.update-status", lead.id),
        { status: lead.status, admin_notes: notesDraft[lead.id] ?? lead.admin_notes },
        { preserveScroll: true }
    );
}

async function destroyLead(lead) {
    if (await confirmDialog({ message: `Hapus lead "${lead.name}"?`, confirmLabel: "Hapus", danger: true })) {
        router.delete(route("admin.leads.destroy", lead.id), { preserveScroll: true });
    }
}

function formatDate(date) {
    return new Date(date).toLocaleString("id-ID", { day: "numeric", month: "short", year: "numeric", hour: "2-digit", minute: "2-digit" });
}
</script>

<template>
    <AdminLayout>
        <template #header>Leads</template>

        <div v-if="page.props.flash?.success" class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded">
            {{ page.props.flash.success }}
        </div>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
            <button
                v-for="status in statusOptions"
                :key="status"
                @click="filterByStatus(filters.status === status ? null : status)"
                class="bg-white rounded-lg shadow p-4 text-left border-2"
                :class="filters.status === status ? 'border-brand-500' : 'border-transparent'"
            >
                <p class="text-2xl font-bold text-gray-900">{{ counts[status] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ statusLabel[status] }}</p>
            </button>
        </div>

        <button v-if="filters.status" @click="filterByStatus(null)" class="mb-4 text-sm text-brand-600 hover:underline">
            Tampilkan semua lead
        </button>

        <div class="bg-white rounded-lg shadow divide-y">
            <EmptyState v-if="!leads.length" message="Belum ada lead." />

            <div v-for="lead in leads" :key="lead.id" class="px-5 py-4">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <p class="font-medium text-gray-900">{{ lead.name }}</p>
                            <span class="text-xs px-2 py-0.5 rounded-full" :class="statusColor[lead.status]">
                                {{ statusLabel[lead.status] }}
                            </span>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-500">{{ lead.source }}</span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            {{ lead.whatsapp }}
                            <span v-if="lead.email"> - {{ lead.email }}</span>
                            <span v-if="lead.service_interested"> - Minat: {{ lead.service_interested }}</span>
                            <span v-if="lead.budget_estimate"> - Anggaran: {{ lead.budget_estimate }}</span>
                        </p>
                        <p v-if="lead.contact_position" class="text-sm text-gray-500 mt-1">Kontak: {{ lead.contact_position }}</p>
                        <p v-if="lead.address" class="text-sm text-gray-500 mt-1">Alamat: {{ lead.address }}</p>
                        <p v-if="lead.message" class="text-sm text-gray-600 mt-2 whitespace-pre-line">{{ lead.message }}</p>
                        <p class="text-xs text-gray-400 mt-2">{{ formatDate(lead.created_at) }}</p>
                        <p v-if="lead.utm_source || lead.referrer || lead.landing_page" class="text-xs text-gray-400 mt-1 break-all">
                            Asal: {{ [lead.utm_source, lead.utm_medium, lead.utm_campaign, lead.referrer, lead.landing_page].filter(Boolean).join(" · ") }}
                        </p>

                        <textarea
                            :value="notesDraft[lead.id] ?? lead.admin_notes ?? ''"
                            @input="notesDraft[lead.id] = $event.target.value"
                            @blur="saveNotes(lead)"
                            placeholder="Catatan internal..."
                            rows="2"
                            class="w-full mt-3 border border-gray-200 rounded-md px-3 py-2 text-xs text-gray-600"
                        ></textarea>
                    </div>

                    <div class="flex flex-col items-end gap-2 flex-shrink-0">
                        <a
                            :href="'https://wa.me/' + lead.whatsapp.replace(/[^0-9]/g, '')"
                            target="_blank"
                            rel="noopener"
                            class="text-xs bg-green-600 text-white px-3 py-1.5 rounded-md hover:bg-green-700"
                        >
                            Chat WA
                        </a>
                        <select
                            :value="lead.status"
                            @change="updateStatus(lead, $event.target.value)"
                            class="text-xs border border-gray-300 rounded-md px-2 py-1"
                        >
                            <option v-for="s in statusOptions" :key="s" :value="s">{{ statusLabel[s] }}</option>
                        </select>
                        <button @click="destroyLead(lead)" class="text-xs text-red-600 hover:underline">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
