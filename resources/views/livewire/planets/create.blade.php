<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center gap-3">
        <flux:button href="{{ route('planets.index') }}" icon="arrow-left" variant="ghost" size="sm" wire:navigate />
        <span class="text-2xl font-semibold text-zinc-800 dark:text-white">Add Planet</span>
    </div>

    <div class="max-w-xl space-y-6">
        <flux:field>
            <flux:label>Sector</flux:label>
            <flux:select wire:model.live="sector" placeholder="Select sector...">
                @foreach($sectors as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </flux:select>
            <flux:error name="sector" />
        </flux:field>

        <flux:field>
            <flux:label>Hex</flux:label>
            <flux:select wire:model="hex" placeholder="{{ blank($sector) ? 'Select a sector first' : 'Select hex...' }}" :disabled="blank($sector)">
                @foreach($this->hexOptions as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </flux:select>
            <flux:error name="hex" />
        </flux:field>

        <flux:field>
            <flux:label>Notes</flux:label>
            <flux:textarea wire:model="notes" rows="4" placeholder="Campaign notes about this world..." />
        </flux:field>

        <div class="flex gap-3">
            <flux:button wire:click="save" variant="primary">Add Planet</flux:button>
            <flux:button href="{{ route('planets.index') }}" variant="ghost" wire:navigate>Cancel</flux:button>
        </div>
    </div>
</div>
