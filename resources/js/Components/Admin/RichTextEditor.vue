<script setup>
/**
 * CKEditor 5 (classic build) wrapper for admin long-form/HTML fields.
 * Only wire this up to fields that are actually rendered with v-html on the
 * public side (e.g. Service.long_description, BlogPost.content) — plain
 * `{{ }}`-rendered fields (Portfolio description, FAQ answers, About
 * copy, etc.) must stay plain textareas or their saved HTML tags would
 * show up as literal text to visitors.
 *
 * Toolbar + heading levels are deliberately capped at what
 * App\Support\HtmlSanitizer::clean() allows server-side
 * (p,br,strong,b,em,i,u,ul,ol,li,a[href],h2,h3,h4,blockquote,img[src|alt],code,pre) —
 * no H1 (the page itself already has one), no table/font-color/etc.
 * Anything picked in the toolbar that isn't in that allowlist would just
 * get silently stripped on save, so the toolbar and the sanitizer must
 * stay in sync if either one changes.
 *
 * Image upload uses a custom UploadAdapter (below) that posts to the same
 * admin.media.store endpoint MediaPicker.vue uses, so inline content images
 * land in the same media library as everything else. imageTextAlternative
 * is enabled so admins get CKEditor's native "Text alternative" prompt for
 * alt text on every inserted image.
 */
import { Ckeditor } from "@ckeditor/ckeditor5-vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

defineProps({
    modelValue: { type: String, default: "" },
});
defineEmits(["update:modelValue"]);

class MediaLibraryUploadAdapter {
    constructor(loader) {
        this.loader = loader;
    }

    upload() {
        return this.loader.file.then(
            (file) =>
                new Promise((resolve, reject) => {
                    const formData = new FormData();
                    formData.append("file", file);

                    window.axios
                        .post(route("admin.media.store"), formData, {
                            headers: { Accept: "application/json", "Content-Type": "multipart/form-data" },
                        })
                        .then((res) => resolve({ default: res.data.url }))
                        .catch((err) => reject(err.response?.data?.errors?.file?.[0] || "Gagal mengunggah gambar."));
                })
        );
    }

    abort() {
        // No cancel token wired up — uploads are small (max 4MB, validated
        // server-side) so an in-flight abort isn't worth the complexity.
    }
}

function MediaLibraryUploadPlugin(editor) {
    editor.plugins.get("FileRepository").createUploadAdapter = (loader) => new MediaLibraryUploadAdapter(loader);
}

const editorConfig = {
    toolbar: [
        "heading", "|",
        "bold", "italic", "|",
        "bulletedList", "numberedList", "|",
        "blockQuote", "link", "uploadImage", "|",
        "undo", "redo",
    ],
    heading: {
        options: [
            { model: "paragraph", title: "Paragraph", class: "ck-heading_paragraph" },
            { model: "heading2", view: "h2", title: "Heading 2", class: "ck-heading_heading2" },
            { model: "heading3", view: "h3", title: "Heading 3", class: "ck-heading_heading3" },
            { model: "heading4", view: "h4", title: "Heading 4", class: "ck-heading_heading4" },
        ],
    },
    link: {
        addTargetToExternalLinks: true,
    },
    image: {
        toolbar: ["imageTextAlternative"],
    },
    extraPlugins: [MediaLibraryUploadPlugin],
};
</script>

<template>
    <div class="rich-text-editor">
        <Ckeditor
            :editor="ClassicEditor"
            :model-value="modelValue"
            :config="editorConfig"
            @update:model-value="$emit('update:modelValue', $event)"
        />
    </div>
</template>

<style scoped>
/* Match the admin form's input border radius/color instead of CKEditor's
   default squared-off gray chrome, so it doesn't look like a foreign
   widget dropped into the rest of the form. */
.rich-text-editor :deep(.ck.ck-editor__editable) {
    min-height: 220px;
}
.rich-text-editor :deep(.ck.ck-toolbar) {
    border-color: #d1d5db;
    border-radius: 0.375rem 0.375rem 0 0;
}
.rich-text-editor :deep(.ck.ck-editor__main > .ck-editor__editable) {
    border-color: #d1d5db;
    border-radius: 0 0 0.375rem 0.375rem;
}
.rich-text-editor :deep(.ck.ck-editor__editable:not(.ck-focused)) {
    border-color: #d1d5db;
}
</style>
