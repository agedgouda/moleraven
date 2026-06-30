<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Animals</flux:heading>
        <flux:button href="{{ route('animals.create') }}" icon="plus" wire:navigate>Add Animal</flux:button>
    </div>

    <flux:input wire:model.live="search" placeholder="Search animals..." icon="magnifying-glass" />

    @if ($animals->isEmpty())
        <div class="rounded-xl border border-dashed border-zinc-300 p-10 text-center dark:border-zinc-700">
            <flux:text class="text-zinc-500">No animals found. <a href="{{ route('animals.create') }}" class="text-blue-500 hover:underline" wire:navigate>Add one.</a></flux:text>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-300">Name</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-300">Behavior</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-300">Native Planet</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-300">Hits</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-300">Speed</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                    @foreach ($animals as $animal)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-4 py-3">
                                <a href="{{ route('animals.edit', $animal) }}" class="inline-flex items-center gap-2.5 font-semibold text-zinc-900 hover:text-blue-600 dark:text-white dark:hover:text-blue-400" wire:navigate>
                                    <x-entity-icon :model="$animal" />
                                    {{ $animal->name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">
                                @if ($animal->behavior_type)
                                    {{ $animal->behavior_type->label() }}{{ $animal->behavior_subtype ? ' / ' . $animal->behavior_subtype->label() : '' }}
                                @else
                                    —
                                @endif
                            </td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $animal->nativePlanet?->display_label ?? '—' }}</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $animal->hits ?? '—' }}</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $animal->speed ? $animal->speed . 'm' : '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <flux:button size="sm" href="{{ route('animals.edit', $animal) }}" wire:navigate icon="pencil" variant="ghost" />
                                    <flux:button size="sm" wire:click="deleteAnimal({{ $animal->id }})" wire:confirm="Delete {{ $animal->name }}?" icon="trash" variant="ghost" class="text-red-500 hover:text-red-700" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
