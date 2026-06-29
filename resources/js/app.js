import { Editor } from '@tiptap/core'
import StarterKit from '@tiptap/starter-kit'

document.addEventListener('alpine:init', () => {
    Alpine.data('tiptap', (initialContent) => ({
        editor: null,

        init() {
            this.editor = new Editor({
                element: this.$refs.content,
                extensions: [StarterKit],
                content: initialContent,
                editorProps: {
                    attributes: {
                        class: 'prose prose-sm dark:prose-invert max-w-none min-h-32 px-4 py-3 focus:outline-none',
                    },
                },
                onUpdate: ({ editor }) => {
                    this.$wire.notes = editor.getHTML()
                },
            })
        },

        destroy() {
            this.editor?.destroy()
        },

        isActive(type, opts = {}) {
            return this.editor?.isActive(type, opts) ?? false
        },
    }))
})
