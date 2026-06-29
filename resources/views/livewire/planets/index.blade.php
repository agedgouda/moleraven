<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Planets</flux:heading>
        <flux:button href="{{ route('planets.create') }}" icon="plus" wire:navigate>Add Planet</flux:button>
    </div>

    <flux:input wire:model.live="search" placeholder="Search by sector or hex..." icon="magnifying-glass" />

    @if ($planets->isEmpty())
        <div class="rounded-xl border border-dashed border-zinc-300 p-10 text-center dark:border-zinc-700">
            <flux:text class="text-zinc-500">No planets found. <a href="{{ route('planets.create') }}" class="text-blue-500 hover:underline" wire:navigate>Add one.</a></flux:text>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-300">Planet</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-300">Sector</th>
                        <th class="px-4 py-3 text-left font-mono font-semibold text-zinc-600 dark:text-zinc-300">Hex</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-300">Notes</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                    @foreach ($planets as $planet)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-4 py-3">
                                <a href="{{ route('planets.edit', $planet) }}" class="font-semibold text-zinc-900 hover:text-blue-600 dark:text-white dark:hover:text-blue-400" wire:navigate>
                                    {{ $planet->display_label }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $planet->sector }}</td>
                            <td class="px-4 py-3 font-mono text-zinc-600 dark:text-zinc-400">{{ $planet->hex }}</td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ Str::limit($planet->notes, 60) }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <flux:button size="sm" href="{{ route('planets.edit', $planet) }}" wire:navigate icon="pencil" variant="ghost" />
                                    <flux:button size="sm" wire:click="deletePlanet({{ $planet->id }})" wire:confirm="Delete this planet?" icon="trash" variant="ghost" class="text-red-500 hover:text-red-700" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
