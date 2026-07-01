<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Party</flux:heading>
    </div>

    <div class="max-w-sm">
        <flux:field>
            <flux:label>Current Planet</flux:label>
            <livewire:components.planet-select wire:model.live="currentPlanetId" />
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
                        <th class="px-4 py-3"></th>
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
                            <td class="px-4 py-3 text-right">
                                <flux:button href="{{ route('diary.create', $character) }}" size="sm" variant="ghost" icon="book-open" wire:navigate>New Diary Entry</flux:button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if ($diaryEntries->isNotEmpty())
        <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
            <div class="border-b border-zinc-200 px-4 py-3 dark:border-zinc-700">
                <flux:heading size="lg">Diary</flux:heading>
            </div>
            @foreach ($diaryEntries as $entry)
                <a href="{{ route('diary.edit', $entry) }}" wire:navigate
                   class="flex items-center gap-3 px-4 py-3 {{ $loop->odd ? 'bg-zinc-200 dark:bg-zinc-800/40' : '' }} hover:bg-zinc-100 dark:hover:bg-zinc-700/40">
                    <x-entity-icon :model="$entry->character" class="h-7 w-7 shrink-0" />
                    <span class="w-28 shrink-0 text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $entry->character->name }}</span>
                    @if ($entry->entry_date)
                        <span class="w-20 shrink-0 font-mono text-sm text-zinc-500">{{ $entry->entry_date }}</span>
                    @endif
                    @if ($entry->entry)
                        <span class="min-w-0 truncate text-sm text-zinc-600 dark:text-zinc-400">{{ Str::limit(strip_tags($entry->entry), 100) }}</span>
                    @endif
                </a>
            @endforeach
        </div>
    @endif

    <div x-data="{ editing: false }" class="flex flex-col gap-2">
        <div class="flex items-center justify-between">
            <flux:label>Notes</flux:label>
            <flux:button x-show="!editing" size="sm" variant="ghost" icon="pencil" @click="editing = true">Edit</flux:button>
        </div>

        {{-- Read mode --}}
        <div x-show="!editing" class="min-h-10 rounded-lg border border-zinc-200 px-4 py-3 dark:border-zinc-700">
            @if ($notes)
                <div class="prose prose-sm dark:prose-invert max-w-none">{!! $notes !!}</div>
            @else
                <span class="text-sm italic text-zinc-400">No notes yet.</span>
            @endif
        </div>

        {{-- Edit mode --}}
        <div x-show="editing">
            <div
                x-data="tiptap(@js($notes ?? ''), 'notes')"
                x-destroy="destroy()"
                wire:ignore
                class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700"
            >
                <div class="flex flex-wrap gap-1 border-b border-zinc-200 bg-zinc-50 px-2 py-1.5 dark:border-zinc-700 dark:bg-zinc-800">
                    <button type="button" @mousedown.prevent @click="run(c => c.toggleBold())" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('bold') }" class="rounded px-2 py-1 text-sm font-bold text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">B</button>
                    <button type="button" @mousedown.prevent @click="run(c => c.toggleItalic())" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('italic') }" class="rounded px-2 py-1 text-sm italic text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">I</button>
                    <button type="button" @mousedown.prevent @click="run(c => c.toggleStrike())" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('strike') }" class="rounded px-2 py-1 text-sm text-zinc-700 line-through hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">S</button>
                    <button type="button" @mousedown.prevent @click="run(c => c.toggleCode())" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('code') }" class="rounded px-2 py-1 font-mono text-sm text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">`</button>
                    <div class="mx-1 w-px bg-zinc-300 dark:bg-zinc-600"></div>
                    <button type="button" @mousedown.prevent @click="run(c => c.toggleHeading({ level: 1 }))" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('heading', { level: 1 }) }" class="rounded px-2 py-1 text-sm font-semibold text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">H1</button>
                    <button type="button" @mousedown.prevent @click="run(c => c.toggleHeading({ level: 2 }))" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('heading', { level: 2 }) }" class="rounded px-2 py-1 text-sm font-semibold text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">H2</button>
                    <button type="button" @mousedown.prevent @click="run(c => c.toggleHeading({ level: 3 }))" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('heading', { level: 3 }) }" class="rounded px-2 py-1 text-sm font-semibold text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">H3</button>
                    <div class="mx-1 w-px bg-zinc-300 dark:bg-zinc-600"></div>
                    <button type="button" @mousedown.prevent @click="run(c => c.toggleBulletList())" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('bulletList') }" class="rounded px-2 py-1 text-sm text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">• List</button>
                    <button type="button" @mousedown.prevent @click="run(c => c.toggleOrderedList())" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('orderedList') }" class="rounded px-2 py-1 text-sm text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">1. List</button>
                    <button type="button" @mousedown.prevent @click="run(c => c.toggleBlockquote())" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('blockquote') }" class="rounded px-2 py-1 text-sm text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">" Quote</button>
                    <div class="mx-1 w-px bg-zinc-300 dark:bg-zinc-600"></div>
                    <button type="button" @mousedown.prevent @click="run(c => c.undo())" class="rounded px-2 py-1 text-sm text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">↩</button>
                    <button type="button" @mousedown.prevent @click="run(c => c.redo())" class="rounded px-2 py-1 text-sm text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">↪</button>
                </div>
                <div x-ref="content" class="bg-white dark:bg-zinc-900"></div>
            </div>
            <div class="mt-2 flex justify-end gap-2">
                <flux:button @click="editing = false" variant="ghost">Cancel</flux:button>
                <flux:button @click="$wire.notes = window._tiptapEditors['notes']?.getHTML() ?? null; $wire.saveNotes(); editing = false" variant="primary">Save Notes</flux:button>
            </div>
        </div>
    </div>
</div>
