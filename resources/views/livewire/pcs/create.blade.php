<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center gap-3">
        <flux:button href="{{ route('pcs.index') }}" icon="arrow-left" variant="ghost" size="sm" wire:navigate />
        <flux:heading size="xl">Add PC</flux:heading>
    </div>

    <div class="max-w-2xl space-y-8">
        {{-- Identity --}}
        <div class="space-y-4 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
            <flux:heading size="lg">Identity</flux:heading>

            <flux:field>
                <flux:label>Name @if(blank($name))<flux:badge color="red" size="sm">Required</flux:badge>@endif</flux:label>
                <flux:input wire:model="name" placeholder="Character name..." autofocus />
                <flux:error name="name" />
            </flux:field>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model="status">
                        @foreach($statusOptions as $s)
                            <option value="{{ $s->value }}">{{ $s->label() }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>

                <flux:field class="flex items-center gap-3 pt-6">
                    <flux:checkbox wire:model="isCurrent" id="is_current" />
                    <flux:label for="is_current">Current PC</flux:label>
                </flux:field>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Age</flux:label>
                    <flux:input type="number" wire:model="age" min="1" max="150" />
                    <flux:error name="age" />
                </flux:field>

                <flux:field>
                    <flux:label>Credits</flux:label>
                    <flux:input type="number" wire:model="credits" min="0" />
                    <flux:error name="credits" />
                </flux:field>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <flux:field>
                    <flux:label>Homeworld</flux:label>
                    <livewire:components.planet-select wire:model="homeworldPlanetId" />
                </flux:field>

                <flux:field>
                    <flux:label>Last Known Planet</flux:label>
                    <livewire:components.planet-select wire:model="lastKnownPlanetId" />
                </flux:field>
            </div>
        </div>

        {{-- UPP --}}
        <div class="space-y-4 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
            <flux:heading size="lg">UPP — Universal Personality Profile</flux:heading>
            <div class="grid grid-cols-3 gap-4">
                @foreach([['strength','STR'],['dexterity','DEX'],['endurance','END'],['intelligence','INT'],['education','EDU'],['socialStanding','SOC']] as [$field, $abbr])
                    <flux:field>
                        <flux:label>{{ $abbr }}</flux:label>
                        <flux:select wire:model="{{ $field }}">
                            @foreach($statOptions as $value => $hex)
                                <option value="{{ $value }}">{{ $hex }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                @endforeach
            </div>
        </div>

        {{-- Notes --}}
        <flux:field>
            <flux:label>Notes</flux:label>
            <flux:textarea wire:model="notes" rows="4" />
        </flux:field>

        <div class="flex gap-3">
            <flux:button wire:click="save" variant="primary">Add PC</flux:button>
            <flux:button href="{{ route('pcs.index') }}" variant="ghost" wire:navigate>Cancel</flux:button>
        </div>
    </div>
</div>
