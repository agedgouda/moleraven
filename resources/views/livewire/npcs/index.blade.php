<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">NPCs</flux:heading>
        <flux:button href="{{ route('npcs.create') }}" icon="plus" wire:navigate>Add NPC</flux:button>
    </div>

    <flux:input wire:model.live="search" placeholder="Search NPCs..." icon="magnifying-glass" />

    @if ($npcs->isEmpty())
        <div class="rounded-xl border border-dashed border-zinc-300 p-10 text-center dark:border-zinc-700">
            <flux:text class="text-zinc-500">No NPCs found. <a href="{{ route('npcs.create') }}" class="text-blue-500 hover:underline" wire:navigate>Add one.</a></flux:text>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-300">Name</th>
                        <th class="px-4 py-3 text-left font-mono font-semibold text-zinc-600 dark:text-zinc-300">UPP</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-300">Age</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-300">Homeworld</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                    @foreach ($npcs as $npc)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-4 py-3">
                                <a href="{{ route('npcs.edit', $npc) }}" class="font-semibold text-zinc-900 hover:text-blue-600 dark:text-white dark:hover:text-blue-400" wire:navigate>
                                    {{ $npc->name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 font-mono tracking-widest text-zinc-700 dark:text-zinc-300">
                                {{ $npc->uppString() }}
                            </td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $npc->age ?? '—' }}</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $npc->homeworld?->display_label ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <flux:button size="sm" href="{{ route('npcs.edit', $npc) }}" wire:navigate icon="pencil" variant="ghost" />
                                    <flux:button size="sm" wire:click="deleteNpc({{ $npc->id }})" wire:confirm="Delete this NPC?" icon="trash" variant="ghost" class="text-red-500 hover:text-red-700" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
