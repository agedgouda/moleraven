<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">Organizations</flux:heading>
        <flux:button href="{{ route('organizations.create') }}" icon="plus" wire:navigate>Add Organization</flux:button>
    </div>

    <flux:input wire:model.live="search" placeholder="Search organizations..." icon="magnifying-glass" />

    @if ($organizations->isEmpty())
        <div class="rounded-xl border border-dashed border-zinc-300 p-10 text-center dark:border-zinc-700">
            <flux:text class="text-zinc-500">No organizations found. <a href="{{ route('organizations.create') }}" class="text-blue-500 hover:underline" wire:navigate>Add one.</a></flux:text>
        </div>
    @else
        <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
            <table class="w-full text-sm">
                <thead class="bg-zinc-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-300">Name</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-300">Type</th>
                        <th class="px-4 py-3 text-left font-semibold text-zinc-600 dark:text-zinc-300">Base of Operations</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                    @foreach ($organizations as $org)
                        <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                            <td class="px-4 py-3">
                                <a href="{{ route('organizations.edit', $org) }}" class="inline-flex items-center gap-2.5 font-semibold text-zinc-900 hover:text-blue-600 dark:text-white dark:hover:text-blue-400" wire:navigate>
                                    <x-entity-icon :model="$org" />
                                    {{ $org->name }}
                                </a>
                            </td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $org->type ?? '—' }}</td>
                            <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400">{{ $org->baseOfOperations?->display_label ?? '—' }}</td>
                            <td class="px-4 py-3 text-right">
                                <div class="flex justify-end gap-2">
                                    <flux:button size="sm" href="{{ route('organizations.edit', $org) }}" wire:navigate icon="pencil" variant="ghost" />
                                    <flux:button size="sm" wire:click="deleteOrganization({{ $org->id }})" wire:confirm="Delete this organization?" icon="trash" variant="ghost" class="text-red-500 hover:text-red-700" />
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
