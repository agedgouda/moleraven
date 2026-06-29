<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center gap-3">
        <flux:button href="{{ route('organizations.index') }}" icon="arrow-left" variant="ghost" size="sm" wire:navigate />
        <flux:heading size="xl">Add Organization</flux:heading>
    </div>

    <div class="max-w-xl space-y-6">
        <flux:field>
            <flux:label>Name @if(blank($name))<flux:badge color="red" size="sm">Required</flux:badge>@endif</flux:label>
            <flux:input wire:model="name" placeholder="Organization name..." autofocus />
            <flux:error name="name" />
        </flux:field>

        <flux:field>
            <flux:label>Type</flux:label>
            <flux:select wire:model="type" placeholder="Select type...">
                @foreach(['Corporation', 'Government', 'Military', 'Criminal', 'Religious', 'Scout Service', 'Mercenary', 'Trade Guild', 'Noble House', 'Other'] as $type)
                    <option value="{{ $type }}">{{ $type }}</option>
                @endforeach
            </flux:select>
        </flux:field>

        <flux:field>
            <flux:label>Base of Operations</flux:label>
            <flux:select wire:model="baseOfOperationsPlanetId" placeholder="Select planet...">
                <option value="">— none —</option>
                @foreach($planets as $id => $label)
                    <option value="{{ $id }}">{{ $label }}</option>
                @endforeach
            </flux:select>
        </flux:field>

        <flux:field>
            <flux:label>Notes</flux:label>
            <flux:textarea wire:model="notes" rows="4" />
        </flux:field>

        <div class="flex gap-3">
            <flux:button wire:click="save" variant="primary">Add Organization</flux:button>
            <flux:button href="{{ route('organizations.index') }}" variant="ghost" wire:navigate>Cancel</flux:button>
        </div>
    </div>
</div>
