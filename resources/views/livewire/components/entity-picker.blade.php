<div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
    <div class="flex items-center justify-between gap-2 px-4 pt-6 pb-0">
        <flux:heading size="lg">{{ $label }}</flux:heading>
    </div>

    <div class="flex gap-2 px-4 pb-3">
        <flux:select wire:model.live="pendingId" class="flex-1">
            <option value="">Select {{ strtolower($label) }}...</option>
            @foreach ($availableOptions as $option)
                <option value="{{ $option->id }}">{{ $option->name }}</option>
            @endforeach
        </flux:select>
        <flux:button size="sm" icon="plus" wire:click="addEntity" :disabled="$pendingId === ''" />
    </div>

    @if ($selectedEntities->isNotEmpty())
        <div class="pb-2">
            @foreach ($selectedEntities as $entity)
                <div class="flex items-center justify-between px-4 py-2 {{ $loop->odd ? 'bg-zinc-100 dark:bg-zinc-800/40' : '' }}">
                    <div class="flex items-center gap-2">
                        @if ($type === 'planet')
                            <x-planet-icon :planet="$entity" class="h-7 w-7 shrink-0" />
                        @endif
                        <span class="text-sm text-zinc-800 dark:text-zinc-200">{{ $entity->name }}</span>
                    </div>
                    <flux:button size="xs" icon="trash" variant="ghost" wire:click="removeEntity({{ $entity->id }})" class="text-red-500" />
                </div>
            @endforeach
        </div>
    @endif
</div>
