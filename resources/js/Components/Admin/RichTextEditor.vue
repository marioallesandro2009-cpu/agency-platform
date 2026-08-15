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
 * (p,br,strong,b,em,i,u,ul,ol,li,a[href],h2,h3,h4,blockquote,img,code,pre) —
 * no H1 (the page itself already has one), no image/media embed, no
 * table/font-color/etc. Anything picked in the toolbar that isn't in that
 * allowlist would just get silently stripped on save, so the toolbar and
 * the sanitizer must stay in sync if either one changes.
 */
import { Ckeditor } from "@ckeditor/ckeditor5-vue";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";

defineProps({
    modelValue: { type: String, default: "" },
});
defineEmits(["update:modelValue"]);

const editorConfig = {
    toolbar: [
        "heading", "|",
        "bold", "italic", "|",
        "bulletedList", "numberedList", "|",
        "blockQuote", "link", "|",
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
