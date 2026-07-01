import { Editor } from '@tiptap/core'
import StarterKit from '@tiptap/starter-kit'

window._tiptapEditors = {}

document.addEventListener('alpine:init', () => {

    Alpine.data('tiptap', (initialContent, wireProperty = 'notes') => {
        let _editor = null

        return {
            updatedAt: null,

            init() {
                _editor = new Editor({
                    element: this.$refs.content,
                    extensions: [StarterKit],
                    content: initialContent,
                    editorProps: {
                        attributes: {
                            class: 'prose prose-sm dark:prose-invert max-w-none min-h-32 px-4 py-3 focus:outline-none',
                        },
                    },
                    onTransaction: () => {
                        this.updatedAt = Date.now()
                    },
                })
                window._tiptapEditors[wireProperty] = _editor
            },

            destroy() {
                _editor?.destroy()
                delete window._tiptapEditors[wireProperty]
                _editor = null
            },

            isActive(type, opts = {}) {
                void this.updatedAt
                return _editor?.isActive(type, opts) ?? false
            },

            run(fn) {
                fn(_editor.chain().focus()).run()
            },
        }
    })
})
