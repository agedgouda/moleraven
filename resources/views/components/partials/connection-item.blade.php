@php $rt = $item['relationshipType']; @endphp
<tr>
    <td class="px-4 py-2">
        <a href="{{ $item['route'] }}" class="inline-flex items-center gap-2 font-medium text-zinc-800 hover:text-blue-600 dark:text-zinc-200" wire:navigate>
            <x-entity-icon :model="$item['model']" />
            {{ $item['name'] }}
        </a>
        @if ($item['notes'])
            <div class="truncate text-xs text-zinc-400">{{ $item['notes'] }}</div>
        @endif
    </td>
    <td class="px-4 py-2 text-zinc-600 dark:text-zinc-300">{{ $item['label'] }}</td>
    <td class="px-4 py-2 text-zinc-600 dark:text-zinc-300">{{ $rt->label() }}</td>
    <td class="px-4 py-2 text-right">
        <div class="flex justify-end gap-1">
            <flux:button size="xs" icon="pencil" variant="ghost" wire:click="openConnectionModal({{ $item['id'] }}, '{{ $item['type'] }}')" />
            <flux:button size="xs" icon="trash" variant="ghost" wire:click="deleteConnection({{ $item['id'] }}, '{{ $item['type'] }}')" wire:confirm="Remove this connection?" class="text-red-500" />
        </div>
    </td>
</tr>
