<div class="flex h-full w-full flex-1 flex-col gap-6 py-6 pl-20 pr-6">
    <div class="flex items-center gap-3">
        <x-entity-icon :model="$organization" class="h-10 w-10 rounded-full" />
        <flux:heading size="xl">{{ $organization->name }}</flux:heading>
    </div>

    {{-- Identity: full width with image on the right --}}
    <div class="flex gap-6 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
        <div class="flex-1 space-y-3">
            <flux:heading size="lg">Identity</flux:heading>
            <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                @if ($organization->type)
                    <div class="text-zinc-500 dark:text-zinc-400">Type</div>
                    <div class="text-zinc-800 dark:text-zinc-200">{{ $organization->type }}</div>
                @endif
                @if ($organization->baseOfOperations)
                    <div class="text-zinc-500 dark:text-zinc-400">Base of Operations</div>
                    <div class="text-zinc-800 dark:text-zinc-200">{{ $organization->baseOfOperations->display_label }}</div>
                @endif
            </div>
        </div>
        <img src="{{ $organization->image_path ? asset('storage/' . $organization->image_path) : asset('images/tas.svg') }}"
             alt="{{ $organization->name }}" class="h-40 w-40 rounded-lg object-cover shrink-0">
    </div>

    {{-- Two columns: connections + notes | summary --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">

            {{-- Connections --}}
            @if ($this->connections->isNotEmpty())
                <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                    <div class="px-6 pt-6 pb-4">
                        <flux:heading size="lg">Connections</flux:heading>
                    </div>
                    <table class="w-full text-sm">
                        <thead class="border-b border-zinc-100 dark:border-zinc-700">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold text-zinc-500 dark:text-zinc-400">Name</th>
                                <th class="px-4 py-2 text-left font-semibold text-zinc-500 dark:text-zinc-400">Type</th>
                                <th class="px-4 py-2 text-left font-semibold text-zinc-500 dark:text-zinc-400">Relation</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                            @foreach ($this->connections as $conn)
                                <tr class="{{ $loop->odd ? 'bg-zinc-200 dark:bg-zinc-800/40' : '' }} {{ $conn['route'] ? 'cursor-pointer hover:bg-zinc-100 dark:hover:bg-zinc-700/40' : '' }}"
                                    @if ($conn['route']) onclick="window.location='{{ $conn['route'] }}'" @endif>
                                    <td class="px-4 py-2">
                                        <span class="inline-flex items-center gap-2 font-medium text-zinc-800 dark:text-zinc-200">
                                            <x-entity-icon :model="$conn['model']" />
                                            {{ $conn['name'] }}
                                        </span>
                                        @if ($conn['notes'])
                                            <div class="truncate text-xs text-zinc-400">{{ $conn['notes'] }}</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-zinc-600 dark:text-zinc-300">{{ $conn['label'] }}</td>
                                    <td class="px-4 py-2 text-zinc-600 dark:text-zinc-300">{{ $conn['relation'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif

            {{-- Notes --}}
            @if ($organization->notes)
                <div class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                    <flux:heading size="lg" class="mb-3">Notes</flux:heading>
                    <p class="text-sm text-zinc-700 dark:text-zinc-300">{{ $organization->notes }}</p>
                </div>
            @endif
        </div>

        {{-- Right column: Summary --}}
        <div class="space-y-4">
            <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="px-6 pt-6 pb-4">
                    <flux:heading size="lg">Summary</flux:heading>
                </div>
                <div class="flex items-center gap-3 px-6 py-2 bg-zinc-200 dark:bg-zinc-800/40">
                    <span class="flex-1 text-sm text-zinc-700 dark:text-zinc-300">Characters</span>
                    <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $organization->characterMemberships()->count() }}</span>
                </div>
                <div class="flex items-center gap-3 px-6 py-2">
                    <span class="flex-1 text-sm text-zinc-700 dark:text-zinc-300">NPCs</span>
                    <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $organization->npcMemberships()->count() }}</span>
                </div>
                <div class="flex items-center gap-3 px-6 py-2 bg-zinc-200 dark:bg-zinc-800/40">
                    <span class="flex-1 text-sm text-zinc-700 dark:text-zinc-300">Organizations</span>
                    <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $organization->orgLinks()->count() + $organization->orgLinkedBy()->count() }}</span>
                </div>
                <div class="pb-2"></div>
            </div>
        </div>
    </div>
</div>
