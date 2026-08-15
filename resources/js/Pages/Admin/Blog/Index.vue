<script setup>
import AdminLayout from "@/Layouts/Admin/AdminLayout.vue";
import EmptyState from "@/Components/Admin/EmptyState.vue";
import MediaPicker from "@/Components/MediaPicker.vue";
import RichTextEditor from "@/Components/Admin/RichTextEditor.vue";
import { router, usePage } from "@inertiajs/vue3";
import { computed, reactive, ref } from "vue";
import { analyzeSeo, seoScoreLabel } from "@/utils/seoAnalyzer";
import { useEscapeClose } from "@/composables/useEscapeClose";
import { useFocusTrap } from "@/composables/useFocusTrap";
import { confirmDialog } from "@/composables/useConfirmDialog";

const props = defineProps({
    posts: Array,
    categories: Array,
});

const page = usePage();
const showForm = ref(false);
const editingId = ref(null);
const newCategoryName = ref("");
const modalRef = ref(null);

useEscapeClose(() => (showForm.value = false));
useFocusTrap(showForm, modalRef);

const form = reactive({
    title: "",
    slug: "",
    blog_category_id: "",
    excerpt: "",
    content: "",
    featured_image: "",
    status: "draft",
    seo_title: "",
    seo_description: "",
    focus_keyword: "",
});

function resetForm() {
    Object.assign(form, {
        title: "", slug: "", blog_category_id: "", excerpt: "", content: "",
        featured_image: "", status: "draft", seo_title: "", seo_description: "", focus_keyword: "",
    });
}

function openCreate() {
    editingId.value = null;
    resetForm();
    showForm.value = true;
}

function openEdit(post) {
    editingId.value = post.id;
    Object.assign(form, {
        title: post.title,
        slug: post.slug,
        blog_category_id: post.blog_category_id || "",
        excerpt: post.excerpt || "",
        content: post.content || "",
        featured_image: post.featured_image || "",
        status: post.status,
        seo_title: post.seo_title || "",
        seo_description: post.seo_description || "",
        focus_keyword: post.focus_keyword || "",
    });
    showForm.value = true;
}

function submit() {
    if (editingId.value) {
        router.put(route("admin.blog.update", editingId.value), { ...form }, { onSuccess: () => (showForm.value = false) });
    } else {
        router.post(route("admin.blog.store"), { ...form }, { onSuccess: () => (showForm.value = false) });
    }
}

async function destroyPost(post) {
    if (await confirmDialog({ message: `Hapus artikel "${post.title}"?`, confirmLabel: "Hapus", danger: true })) {
        router.delete(route("admin.blog.destroy", post.id), { preserveScroll: true });
    }
}

function addCategory() {
    if (!newCategoryName.value.trim()) return;
    router.post(route("admin.blog-categories.store"), { name: newCategoryName.value }, {
        preserveScroll: true,
        onSuccess: () => (newCategoryName.value = ""),
    });
}

async function deleteCategory(cat) {
    if (await confirmDialog({ message: `Hapus kategori "${cat.name}"?`, confirmLabel: "Hapus", danger: true })) {
        router.delete(route("admin.blog-categories.destroy", cat.id), { preserveScroll: true });
    }
}

const statusLabel = { draft: "Draft", published: "Published", scheduled: "Scheduled" };
const statusColor = {
    draft: "bg-gray-100 text-gray-500",
    published: "bg-green-100 text-green-700",
    scheduled: "bg-yellow-100 text-yellow-700",
};

const seoAnalysis = computed(() => analyzeSeo({
    title: form.title,
    slug: form.slug,
    content: form.content,
    seoTitle: form.seo_title,
    seoDescription: form.seo_description,
    focusKeyword: form.focus_keyword,
}));

function postSeoScore(post) {
    if (!post.focus_keyword) return null;
    return analyzeSeo({
        title: post.title,
        slug: post.slug,
        content: post.content,
        seoTitle: post.seo_title,
        seoDescription: post.seo_description,
        focusKeyword: post.focus_keyword,
    }).score;
}

function scoreDotColor(score) {
    if (score >= 80) return "bg-green-500";
    if (score >= 50) return "bg-yellow-500";
    return "bg-red-500";
}

function scorePanelColor(score) {
    if (score >= 80) return "bg-green-50 border-green-200";
    if (score >= 50) return "bg-yellow-50 border-yellow-200";
    return "bg-red-50 border-red-200";
}

function checkDotColor(status) {
    return { good: "bg-green-500", ok: "bg-yellow-500", bad: "bg-red-500" }[status];
}
</script>

<template>
    <AdminLayout>
        <template #header>Blog</template>

        <div v-if="page.props.flash?.success" class="mb-4 bg-green-100 text-green-800 px-4 py-2 rounded">
            {{ page.props.flash.success }}
        </div>

        <div class="flex items-center justify-between mb-4">
            <button @click="openCreate" class="bg-brand-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-brand-700">
                + Tulis Artikel
            </button>
        </div>

        <!-- Category quick manager -->
        <div class="bg-white rounded-lg shadow p-4 mb-6">
            <p class="text-sm font-semibold text-gray-700 mb-2">Kategori</p>
            <div class="flex flex-wrap gap-2 mb-3">
                <span v-for="cat in categories" :key="cat.id" class="inline-flex items-center gap-1 bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded-full">
                    {{ cat.name }}
                    <button @click="deleteCategory(cat)" class="text-gray-400 hover:text-red-600">x</button>
                </span>
            </div>
            <div class="flex gap-2">
                <input v-model="newCategoryName" type="text" placeholder="Nama kategori baru" class="border rounded-md px-3 py-1.5 text-sm flex-1" @keyup.enter="addCategory" />
                <button @click="addCategory" class="bg-gray-800 text-white text-xs font-medium px-3 py-1.5 rounded-md">Tambah</button>
            </div>
        </div>

        <!-- Posts list -->
        <div class="bg-white rounded-lg shadow divide-y">
            <div v-for="post in posts" :key="post.id" class="flex items-center justify-between px-5 py-4">
                <div>
                    <p class="font-medium text-gray-900">{{ post.title }}</p>
                    <p class="text-xs text-gray-400">
                        {{ post.category?.name || "Tanpa kategori" }}
                        <span v-if="post.published_at"> - {{ new Date(post.published_at).toLocaleDateString("id-ID") }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <span
                        v-if="postSeoScore(post) !== null"
                        class="inline-flex items-center gap-1.5 text-xs font-medium px-2 py-1 rounded-full bg-gray-50 border border-gray-200"
                        title="SEO score"
                    >
                        <span class="w-2 h-2 rounded-full" :class="scoreDotColor(postSeoScore(post))"></span>
                        SEO {{ postSeoScore(post) }}
                    </span>
                    <span :class="statusColor[post.status]" class="text-xs font-medium px-3 py-1 rounded-full">
                        {{ statusLabel[post.status] }}
                    </span>
                    <button @click="openEdit(post)" class="text-sm text-brand-600 hover:underline">Edit</button>
                    <button @click="destroyPost(post)" class="text-sm text-red-600 hover:underline">Hapus</button>
                </div>
            </div>
            <EmptyState v-if="posts.length === 0" message="Belum ada artikel." />
        </div>

        <!-- Form modal -->
        <div v-if="showForm" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 p-4" @click.self="showForm = false">
            <div ref="modalRef" role="dialog" aria-modal="true" aria-labelledby="blog-form-title" class="bg-white rounded-lg shadow-xl max-w-2xl w-full p-6 max-h-[90vh] overflow-auto">
                <h2 id="blog-form-title" class="text-lg font-semibold mb-4">{{ editingId ? "Edit" : "Tulis" }} Artikel</h2>
                <form @submit.prevent="submit" class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Judul</label>
                        <input v-model="form.title" type="text" required class="w-full border rounded-md px-3 py-2 text-sm" />
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-sm font-medium mb-1">Slug (kosongkan buat auto)</label>
                            <input v-model="form.slug" type="text" class="w-full border rounded-md px-3 py-2 text-sm" />
                        </div>
                        <div>
                            <label class="block text-sm font-medium mb-1">Kategori</label>
                            <select v-model="form.blog_category_id" class="w-full border rounded-md px-3 py-2 text-sm">
                                <option value="">- Tanpa kategori -</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Excerpt (ringkasan)</label>
                        <textarea v-model="form.excerpt" class="w-full border rounded-md px-3 py-2 text-sm" rows="2"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Konten Artikel</label>
                        <RichTextEditor v-model="form.content" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Featured Image</label>
                        <MediaPicker v-model="form.featured_image" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Status</label>
                        <select v-model="form.status" class="w-full border rounded-md px-3 py-2 text-sm">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>

                    <div class="border-t pt-3 mt-3">
                        <p class="text-sm font-semibold text-gray-700 mb-2">SEO</p>
                        <div class="space-y-2">
                            <input v-model="form.focus_keyword" type="text" placeholder="Focus keyword" class="w-full border rounded-md px-3 py-2 text-sm" />
                            <input v-model="form.seo_title" type="text" placeholder="SEO Title" class="w-full border rounded-md px-3 py-2 text-sm" />
                            <textarea v-model="form.seo_description" placeholder="Meta description" class="w-full border rounded-md px-3 py-2 text-sm" rows="2"></textarea>
                        </div>

                        <div v-if="seoAnalysis.hasKeyword" class="mt-3 border rounded-md p-3" :class="scorePanelColor(seoAnalysis.score)">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-semibold text-gray-800">SEO Score</p>
                                <span class="text-sm font-bold text-gray-800">{{ seoAnalysis.score }}/100 - {{ seoScoreLabel(seoAnalysis.score) }}</span>
                            </div>
                            <ul class="space-y-1.5">
                                <li v-for="check in seoAnalysis.checks" :key="check.id" class="text-xs text-gray-600 flex items-start gap-2">
                                    <span class="w-2 h-2 rounded-full mt-1 flex-shrink-0" :class="checkDotColor(check.status)"></span>
                                    <span>{{ check.message }}</span>
                                </li>
                            </ul>
                        </div>
                        <p v-else class="text-xs text-gray-400 mt-2">Isi focus keyword untuk melihat analisis SEO.</p>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showForm = false" class="px-4 py-2 text-sm text-gray-600">Batal</button>
                        <button type="submit" class="bg-brand-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-brand-700">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>