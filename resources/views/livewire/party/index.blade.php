<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Party</flux:heading>
    </div>

    <div class="max-w-sm">
        <flux:field>
            <flux:label>Current Planet</flux:label>
            <flux:select wire:model.live="currentPlanetId" placeholder="Select planet...">
                <option value="">— none —</option>
                @foreach($planets as $id => $label)
                    <option value="{{ $id }}">{{ $label }}</option>
                @endforeach
            </flux:select>
        </flux:field>
    </div>

    @if ($characters->isEmpty())
        <div class="rounded-xl border border-dashed border-zinc-300 p-10 text-center dark:border-zinc-700">
            <flux:text class="text-zinc-500">No active PCs yet.</flux:text>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-300">Name</th>
                        <th class="px-4 py-3 text-left font-mono font-semibold text-zinc-600 dark:text-zinc-300">UPP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                    @foreach ($characters as $character)
                        <tr class="group hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-4 py-3">
                                <a href="{{ route('pcs.edit', $character) }}" class="inline-flex items-center gap-2.5 font-semibold text-zinc-900 hover:text-blue-600 dark:text-white dark:hover:text-blue-400" wire:navigate>
                                    <x-entity-icon :model="$character" />
                                    {{ $character->name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 font-mono tracking-widest text-zinc-700 dark:text-zinc-300">
                                {{ $character->uppString() }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="flex flex-col gap-2">
        <flux:label>Notes</flux:label>
        <div
            x-data="tiptap(@js($notes ?? ''))"
            x-init="init()"
            x-destroy="destroy()"
            class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700"
        >
            {{-- Toolbar --}}
            <div class="flex flex-wrap gap-1 border-b border-zinc-200 bg-zinc-50 px-2 py-1.5 dark:border-zinc-700 dark:bg-zinc-800">
                <button type="button" @click="editor.chain().focus().toggleBold().run()" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('bold') }" class="rounded px-2 py-1 text-sm font-bold text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">B</button>
                <button type="button" @click="editor.chain().focus().toggleItalic().run()" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('italic') }" class="rounded px-2 py-1 text-sm italic text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">I</button>
                <button type="button" @click="editor.chain().focus().toggleStrike().run()" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('strike') }" class="rounded px-2 py-1 text-sm text-zinc-700 line-through hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">S</button>
                <button type="button" @click="editor.chain().focus().toggleCode().run()" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('code') }" class="rounded px-2 py-1 font-mono text-sm text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">`</button>
                <div class="mx-1 w-px bg-zinc-300 dark:bg-zinc-600"></div>
                <button type="button" @click="editor.chain().focus().toggleHeading({ level: 1 }).run()" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('heading', { level: 1 }) }" class="rounded px-2 py-1 text-sm font-semibold text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">H1</button>
                <button type="button" @click="editor.chain().focus().toggleHeading({ level: 2 }).run()" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('heading', { level: 2 }) }" class="rounded px-2 py-1 text-sm font-semibold text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">H2</button>
                <button type="button" @click="editor.chain().focus().toggleHeading({ level: 3 }).run()" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('heading', { level: 3 }) }" class="rounded px-2 py-1 text-sm font-semibold text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">H3</button>
                <div class="mx-1 w-px bg-zinc-300 dark:bg-zinc-600"></div>
                <button type="button" @click="editor.chain().focus().toggleBulletList().run()" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('bulletList') }" class="rounded px-2 py-1 text-sm text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">• List</button>
                <button type="button" @click="editor.chain().focus().toggleOrderedList().run()" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('orderedList') }" class="rounded px-2 py-1 text-sm text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">1. List</button>
                <button type="button" @click="editor.chain().focus().toggleBlockquote().run()" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('blockquote') }" class="rounded px-2 py-1 text-sm text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">" Quote</button>
                <div class="mx-1 w-px bg-zinc-300 dark:bg-zinc-600"></div>
                <button type="button" @click="editor.chain().focus().undo().run()" class="rounded px-2 py-1 text-sm text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">↩</button>
                <button type="button" @click="editor.chain().focus().redo().run()" class="rounded px-2 py-1 text-sm text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">↪</button>
            </div>
            {{-- Editor --}}
            <div x-ref="content" class="bg-white dark:bg-zinc-900"></div>
        </div>
        <div class="flex justify-end">
            <flux:button wire:click="saveNotes" variant="primary">Save Notes</flux:button>
        </div>
    </div>
</div>
