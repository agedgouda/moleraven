<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center justify-between">
        <flux:heading size="xl">
            {{ $parentAnimal ? 'Add Variant of ' . $parentAnimal->name : 'New Animal' }}
        </flux:heading>
        <flux:button href="{{ route('animals.index') }}" variant="ghost" icon="arrow-left" wire:navigate>Animals</flux:button>
    </div>

    <div class="max-w-sm">
        <flux:field>
            <flux:label>Name <flux:badge color="red" size="sm">Required</flux:badge></flux:label>
            <flux:input wire:model="name" placeholder="Species name" autofocus />
            <flux:error name="name" />
        </flux:field>

        <div class="mt-6 flex justify-end">
            <flux:button wire:click="save" variant="primary">Create Animal</flux:button>
        </div>
    </div>
</div>
