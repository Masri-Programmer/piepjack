<template>
    <div v-if="editor" class="shadow-xl rounded-xl me-5">
        <div class="control-group">
            <div
                class="h-12 border-b [border-image:linear-gradient(to_right,transparent,var(--color-slate-200),transparent)1]"
            >
                <div
                    class="h-full flex justify-between items-center sm:gap-1 overflow-x-scroll [scrollbar-width:none] px-3"
                >
                    <button
                        class="p-1 rounded text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                        @click="editor.chain().focus().toggleBold().run()"
                        :disabled="
                            !editor.can().chain().focus().toggleBold().run()
                        "
                        :class="{ 'bg-slate-100': editor.isActive('bold') }"
                        aria-label="Bold"
                        title="Bold"
                    >
                        <Bold size="18" />
                    </button>

                    <button
                        class="p-1 rounded text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                        @click="editor.chain().focus().toggleItalic().run()"
                        :disabled="
                            !editor.can().chain().focus().toggleItalic().run()
                        "
                        :class="{ 'bg-slate-100': editor.isActive('italic') }"
                        aria-label="Italic"
                        title="Italic"
                    >
                        <Italic size="18" />
                    </button>

                    <button
                        class="p-1 rounded text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                        @click="editor.chain().focus().toggleStrike().run()"
                        :disabled="
                            !editor.can().chain().focus().toggleStrike().run()
                        "
                        :class="{ 'bg-slate-100': editor.isActive('strike') }"
                        aria-label="Strikethrough"
                        title="Strikethrough"
                    >
                        <Strikethrough size="18" />
                    </button>

                    <button
                        class="p-1 rounded text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                        @click="editor.chain().focus().toggleCode().run()"
                        :disabled="
                            !editor.can().chain().focus().toggleCode().run()
                        "
                        :class="{ 'bg-slate-100': editor.isActive('code') }"
                        aria-label="Code"
                        title="Code"
                    >
                        <Code size="18" />
                    </button>

                    <button
                        class="p-1 rounded text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                        @click="editor.chain().focus().unsetAllMarks().run()"
                        aria-label="Remove Formatting"
                        title="Remove Formatting"
                    >
                        <RemoveFormatting size="18" />
                    </button>

                    <button
                        class="p-1 rounded text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                        @click="editor.chain().focus().setParagraph().run()"
                        :class="{
                            'bg-slate-100': editor.isActive('paragraph'),
                        }"
                        aria-label="Paragraph"
                        title="Paragraph"
                    >
                        <List size="18" />
                    </button>

                    <button
                        class="p-1 rounded text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                        @click="
                            editor
                                .chain()
                                .focus()
                                .toggleHeading({ level: 1 })
                                .run()
                        "
                        :class="{
                            'bg-slate-100': editor.isActive('heading', {
                                level: 1,
                            }),
                        }"
                        aria-label="Heading 1"
                        title="Heading 1"
                    >
                        <Heading1 size="18" />
                    </button>

                    <button
                        class="p-1 rounded text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                        @click="
                            editor
                                .chain()
                                .focus()
                                .toggleHeading({ level: 2 })
                                .run()
                        "
                        :class="{
                            'bg-slate-100': editor.isActive('heading', {
                                level: 2,
                            }),
                        }"
                        aria-label="Heading 2"
                        title="Heading 2"
                    >
                        <Heading2 size="18" />
                    </button>
                    <button
                        class="p-1 rounded text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                        @click="
                            editor
                                .chain()
                                .focus()
                                .toggleHeading({ level: 3 })
                                .run()
                        "
                        :class="{
                            'bg-slate-100': editor.isActive('heading', {
                                level: 3,
                            }),
                        }"
                        aria-label="Heading level 3"
                        title="Heading level 3"
                    >
                        <Heading3 size="18" />
                    </button>

                    <button
                        class="p-1 rounded text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                        @click="
                            editor
                                .chain()
                                .focus()
                                .toggleHeading({ level: 4 })
                                .run()
                        "
                        :class="{
                            'bg-slate-100': editor.isActive('heading', {
                                level: 4,
                            }),
                        }"
                        aria-label="Heading level 4"
                        title="Heading level 4"
                    >
                        <Heading4 size="18" />
                    </button>

                    <button
                        class="p-1 rounded text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                        @click="
                            editor
                                .chain()
                                .focus()
                                .toggleHeading({ level: 5 })
                                .run()
                        "
                        :class="{
                            'bg-slate-100': editor.isActive('heading', {
                                level: 5,
                            }),
                        }"
                        aria-label="Heading level 5"
                        title="Heading level 5"
                    >
                        <Heading5 size="18" />
                    </button>

                    <button
                        class="p-1 rounded text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                        @click="
                            editor
                                .chain()
                                .focus()
                                .toggleHeading({ level: 6 })
                                .run()
                        "
                        :class="{
                            'bg-slate-100': editor.isActive('heading', {
                                level: 6,
                            }),
                        }"
                        aria-label="Heading level 6"
                        title="Heading level 6"
                    >
                        <Heading6 size="18" />
                    </button>

                    <button
                        class="p-1 rounded text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                        @click="editor.chain().focus().toggleBulletList().run()"
                        :class="{
                            'bg-slate-100': editor.isActive('bulletList'),
                        }"
                        aria-label="Bullet list"
                        title="Bullet list"
                    >
                        <Pilcrow size="18" />
                    </button>
                    <button
                        class="p-1 rounded text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                        @click.prevent="
                            editor.chain().focus().toggleUnderline().run()
                        "
                        :class="{
                            'bg-slate-100': editor.isActive('bulletList'),
                        }"
                        aria-label="Bullet list"
                        title="Bullet list"
                    >
                        <Underline size="18" />
                    </button>

                    <button
                        class="p-1 rounded text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                        @click="
                            editor.chain().focus().toggleOrderedList().run()
                        "
                        :class="{
                            'bg-slate-100': editor.isActive('orderedList'),
                        }"
                        aria-label="Ordered list"
                        title="Ordered list"
                    >
                        <ListOrdered size="18" />
                    </button>

                    <input
                        type="color"
                        class="w-8 h-8 border-0 rounded-full focus-visible:ring focus-visible:ring-indigo-300"
                        @change="
                            (e) =>
                                editor
                                    .chain()
                                    .focus()
                                    .setColor(e.target.value)
                                    .run()
                        "
                    />
                </div>
            </div>
        </div>
        <editor-content
            :editor="editor"
            class="w-full px-3 py-3 text-sm resize-none text-slate-600 placeholder:text-slate-400 sm:px-3 focus:outline-none min-h-32"
        />
        <footer>
            <div
                class="h-12 border-t [border-image:linear-gradient(to_right,transparent,var(--color-slate-200),transparent)1]"
            >
                <div
                    class="h-full flex justify-between items-center sm:gap-1 overflow-x-scroll [scrollbar-width:none] px-3"
                >
                    <!-- Left actions -->
                    <div class="flex items-center">
                        <!-- Delete button -->
                        <div
                            class="pr-5 relative flex items-center after:absolute after:right-2.5 after:h-6 after:w-px after:bg-slate-200"
                        >
                            <button
                                class="flex items-center justify-center text-red-400 rounded size-8 hover:text-red-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                                @click="
                                    editor.chain().focus().clearNodes().run()
                                "
                                aria-label="Delete"
                                title="Delete"
                            >
                                <Trash2 size="18" />
                            </button>
                        </div>
                        <!-- More buttons -->
                        <div class="flex sm:gap-1">
                            <button
                                class="p-1 rounded text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                                @click="
                                    editor
                                        .chain()
                                        .focus()
                                        .toggleBlockquote()
                                        .run()
                                "
                                :class="{
                                    'bg-slate-100':
                                        editor.isActive('blockquote'),
                                }"
                                aria-label="Blockquote"
                                title="Blockquote"
                            >
                                <MessageSquareQuote size="18" />
                            </button>

                            <button
                                class="p-1 rounded text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                                @click="
                                    editor
                                        .chain()
                                        .focus()
                                        .setHorizontalRule()
                                        .run()
                                "
                                aria-label="Horizontal rule"
                                title="Horizontal rule"
                            >
                                <Rows2 size="18" />
                            </button>

                            <button
                                class="p-1 rounded text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                                @click="
                                    editor.chain().focus().setHardBreak().run()
                                "
                                aria-label="Hard break"
                                title="Hard break"
                            >
                                <WrapText size="18" />
                            </button>

                            <button
                                class="p-1 rounded text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                                @click="editor.chain().focus().undo().run()"
                                :disabled="
                                    !editor.can().chain().focus().undo().run()
                                "
                                aria-label="Undo"
                                title="Undo"
                            >
                                <Undo size="18" />
                            </button>

                            <button
                                class="p-1 rounded text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                                @click="editor.chain().focus().redo().run()"
                                :disabled="
                                    !editor.can().chain().focus().redo().run()
                                "
                                aria-label="Redo"
                                title="Redo"
                            >
                                <Redo size="18" />
                            </button>
                            <button
                                class="flex items-center justify-center rounded size-8 text-slate-400 hover:text-slate-500 hover:bg-slate-100 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300"
                                aria-label="More actions"
                                title="More actions"
                            >
                                <svg
                                    class="fill-current"
                                    xmlns="http://www.w3.org/2000/svg"
                                    width="16"
                                    height="4"
                                >
                                    <path
                                        d="M2 4a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm6 0a2 2 0 1 0 0-4 2 2 0 0 0 0 4Zm8-2a2 2 0 1 1-4 0 2 2 0 0 1 4 0Z"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <!-- Right actions -->
                    <div>
                        <button
                            class="inline-flex items-center justify-center h-8 px-2 text-sm font-medium text-indigo-500 transition-colors rounded-lg whitespace-nowrap hover:text-white hover:bg-indigo-500 focus-visible:text-white focus-visible:bg-indigo-500 focus-visible:outline-none focus-visible:ring focus-visible:ring-indigo-300 dark:focus-visible:ring-slate-600"
                            @click="emitEditorContent"
                        >
                            Send
                        </button>
                    </div>
                </div>
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { EditorContent, Editor } from "@tiptap/vue-3";
import StarterKit from "@tiptap/starter-kit";
import Color from "@tiptap/extension-color";
import ListItem from "@tiptap/extension-list-item";
import TextStyle from "@tiptap/extension-text-style";
import {
    Bold,
    Code,
    List,
    Undo,
    Redo,
    Rows2,
    Italic,
    Trash2,
    Pilcrow,
    Heading1,
    WrapText,
    Heading2,
    Heading3,
    Heading4,
    Heading5,
    Heading6,
    ListOrdered,
    Strikethrough,
    RemoveFormatting,
    Underline,
    MessageSquareQuote,
} from "lucide-vue-next";
const props = defineProps({
    desc: { type: String },
});
const emit = defineEmits("handle:editor");
const editor = ref(null);

onMounted(() => {
    editor.value = new Editor({
        extensions: [
            Color.configure({ types: [TextStyle.name, ListItem.name] }),
            TextStyle.configure({ types: [ListItem.name] }),
            StarterKit,
        ],
        content: props.desc,
    });
});

onBeforeUnmount(() => {
    editor.value?.destroy();
});
const emitEditorContent = () => {
    emit("handle:editor", editor.value.getHTML());
};
</script>
