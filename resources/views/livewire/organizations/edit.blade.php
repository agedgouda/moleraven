<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center gap-3">
        <flux:button href="{{ route('organizations.index') }}" icon="arrow-left" variant="ghost" size="sm" wire:navigate />
        <flux:heading size="xl">{{ $organization->name }}</flux:heading>
    </div>

    <div class="max-w-xl space-y-6">
        <flux:field>
            <flux:label>Name <flux:badge color="red" size="sm">Required</flux:badge></flux:label>
            <flux:input wire:model="name" />
            <flux:error name="name" />
        </flux:field>

        <flux:field>
            <flux:label>Type</flux:label>
            <flux:select wire:model="type" placeholder="Select type...">
                <option value="">— none —</option>
                @foreach($types as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </flux:select>
        </flux:field>

        <flux:field>
            <flux:label>Base of Operations</flux:label>
            <flux:input wire:model="baseOfOperations" placeholder="Location or system..." />
        </flux:field>

        <flux:field>
            <flux:label>Notes</flux:label>
            <flux:textarea wire:model="notes" rows="4" />
        </flux:field>

        <div class="flex gap-3">
            <flux:button wire:click="save" variant="primary">Save</flux:button>
            <flux:button href="{{ route('organizations.index') }}" variant="ghost" wire:navigate>Back</flux:button>
        </div>
    </div>
</div>
