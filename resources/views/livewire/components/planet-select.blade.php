<div>
    <flux:select wire:model.live="selected" placeholder="Select planet...">
        <option value="">— none —</option>
        <option value="new">+ Add planet...</option>
        @foreach($this->planets as $id => $label)
            <option value="{{ $id }}">{{ $label }}</option>
        @endforeach
    </flux:select>

    <flux:modal wire:model="modalOpen" class="md:w-96">
        <flux:heading>Add Planet</flux:heading>
        <div class="mt-4 space-y-4">
            <flux:field>
                <flux:label>Sector @if(blank($newSector))<flux:badge color="red" size="sm">Required</flux:badge>@endif</flux:label>
                <flux:select wire:model.live="newSector" placeholder="Select sector...">
                    <option value="">— select sector —</option>
                    @foreach($sectors as $sector)
                        <option value="{{ $sector }}">{{ $sector }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="newSector" />
            </flux:field>
            <flux:field>
                <flux:label>World @if(blank($newHex))<flux:badge color="red" size="sm">Required</flux:badge>@endif</flux:label>
                <flux:select wire:model="newHex" placeholder="Select world..." :disabled="blank($newSector)">
                    <option value="">— select world —</option>
                    @foreach($this->hexOptions as $hex => $name)
                        <option value="{{ $hex }}">{{ $name }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="newHex" />
            </flux:field>
        </div>
        <div class="mt-6 flex justify-end gap-2">
            <flux:modal.close><flux:button variant="ghost">Cancel</flux:button></flux:modal.close>
            <flux:button wire:click="createPlanet" variant="primary">Add Planet</flux:button>
        </div>
    </flux:modal>
</div>
