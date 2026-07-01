<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center gap-3">
        <flux:button href="{{ route('party') }}" icon="arrow-left" variant="ghost" size="sm" wire:navigate />
        <span class="text-2xl font-semibold text-zinc-800 dark:text-white">New Diary Entry</span>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        {{-- Left column: date + entry --}}
        <div class="space-y-6 xl:col-span-2">
            <div class="flex items-center gap-3">
                <x-entity-icon :model="$character" class="h-8 w-8 shrink-0 rounded-full" />
                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $character->name }}</span>
                <div class="ml-auto w-32"><flux:input wire:model="entryDate" placeholder="1105-001" /></div>
            </div>
            <flux:error name="entryDate" />

            <div class="flex flex-col gap-2">
                <flux:label>Entry</flux:label>
                <div
                    x-data="tiptap(@js($entry ?? ''), 'entry')"
                    x-destroy="destroy()"
                    wire:ignore
                    class="overflow-hidden rounded-lg border border-zinc-200 dark:border-zinc-700"
                >
                    <div class="flex flex-wrap gap-1 border-b border-zinc-200 bg-zinc-50 px-2 py-1.5 dark:border-zinc-700 dark:bg-zinc-800">
                        <button type="button" @mousedown.prevent @click="run(c => c.toggleBold())" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('bold') }" class="rounded px-2 py-1 text-sm font-bold text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">B</button>
                        <button type="button" @mousedown.prevent @click="run(c => c.toggleItalic())" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('italic') }" class="rounded px-2 py-1 text-sm italic text-zinc-700 hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">I</button>
                        <button type="button" @mousedown.prevent @click="run(c => c.toggleStrike())" :class="{ 'bg-zinc-200 dark:bg-zinc-600': isActive('strike') }" class="rounded px-2 py-1 text-sm text-zinc-700 line-through hover:bg-zinc-200 dark:text-zinc-300 dark:hover:bg-zinc-600">S</button>
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
                <flux:error name="entry" />
            </div>

            <div class="flex gap-3">
                <flux:button @click="$wire.entry = window._tiptapEditors['entry']?.getHTML() ?? null; $wire.save()" variant="primary">Save Entry</flux:button>
                <flux:button href="{{ route('party') }}" variant="ghost" wire:navigate>Cancel</flux:button>
            </div>
        </div>

        {{-- Right column: encountered entities --}}
        <div class="space-y-6">
            <livewire:components.entity-picker label="Planets" type="planet" wire:model="planetIds" :default-id="$character->last_known_planet_id" />
            <livewire:components.entity-picker label="NPCs" type="npc" wire:model="npcIds" />
            <livewire:components.entity-picker label="Organizations" type="organization" wire:model="organizationIds" />
            <livewire:components.entity-picker label="Animals" type="animal" wire:model="animalIds" />
        </div>
    </div>
</div>
