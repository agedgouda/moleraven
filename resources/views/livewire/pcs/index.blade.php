<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">PCs</flux:heading>
        <flux:button href="{{ route('pcs.create') }}" icon="plus" wire:navigate>Add PC</flux:button>
    </div>

    <flux:input wire:model.live="search" placeholder="Search PCs..." icon="magnifying-glass" />

    @if ($characters->isEmpty())
        <div class="rounded-xl border border-dashed border-zinc-300 p-10 text-center dark:border-zinc-700">
            <flux:text class="text-zinc-500">No PCs found. <a href="{{ route('pcs.create') }}" class="text-blue-500 hover:underline" wire:navigate>Add one.</a></flux:text>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-300">Name</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-300">Player</th>
                        <th class="px-4 py-3 text-left font-mono font-semibold text-zinc-600 dark:text-zinc-300">UPP</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-300">Status</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-300">Age</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-300">Credits</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-300">Current</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                    @foreach ($characters as $character)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-4 py-3">
                                <a href="{{ route('pcs.edit', $character) }}" class="font-semibold text-zinc-900 hover:text-blue-600 dark:text-white dark:hover:text-blue-400" wire:navigate>
                                    {{ $character->name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $character->user->name }}</td>
                            <td class="px-4 py-3 font-mono tracking-widest text-zinc-700 dark:text-zinc-300">{{ $character->uppString() }}</td>
                            <td class="px-4 py-3">
                                @php $status = $character->status; @endphp
                                <span class="rounded-full px-2 py-0.5 text-xs font-semibold
                                    @if($status->color() === 'success') bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                    @elseif($status->color() === 'warning') bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400
                                    @else bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400 @endif">
                                    {{ $status->label() }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $character->age ?? '—' }}</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ number_format($character->credits ?? 0) }} Cr</td>
                            <td class="px-4 py-3">
                                @if($character->is_current)
                                    <span class="rounded-full bg-blue-100 px-2 py-0.5 text-xs font-semibold text-blue-800 dark:bg-blue-900/30 dark:text-blue-400">Current</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <flux:button size="sm" href="{{ route('pcs.edit', $character) }}" wire:navigate icon="pencil" variant="ghost" />
                                    <flux:button size="sm" wire:click="deleteCharacter({{ $character->id }})" wire:confirm="Delete {{ $character->name }}?" icon="trash" variant="ghost" class="text-red-500 hover:text-red-700" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
