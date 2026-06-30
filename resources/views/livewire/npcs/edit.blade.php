<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center gap-3">
        <flux:button href="{{ route('npcs.index') }}" icon="arrow-left" variant="ghost" size="sm" wire:navigate />
        <x-entity-icon :model="$npc" class="h-10 w-10 rounded-full" />
        <flux:heading size="xl">{{ $npc->name }}</flux:heading>
        <span class="font-mono text-lg tracking-widest text-zinc-500 dark:text-zinc-400">{{ $npc->uppString() }}</span>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        {{-- Main form (2/3 width) --}}
        <div class="space-y-8 lg:col-span-2">
            {{-- Identity --}}
            <div class="space-y-4 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                <flux:heading size="lg">Identity</flux:heading>

                <flux:field>
                    <flux:label>Name @if(blank($name))<flux:badge color="red" size="sm">Required</flux:badge>@endif</flux:label>
                    <flux:input wire:model="name" />
                    <flux:error name="name" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Homeworld</flux:label>
                        <flux:select wire:model="homeworldPlanetId" placeholder="Select planet...">
                            <option value="">— none —</option>
                            @foreach($planets as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:field>
                        <flux:label>Last Known Planet</flux:label>
                        <flux:select wire:model="lastKnownPlanetId" placeholder="Select planet...">
                            <option value="">— none —</option>
                            @foreach($planets as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>

                <flux:field class="max-w-xs">
                    <flux:label>Age</flux:label>
                    <flux:input type="number" wire:model="age" min="1" max="150" />
                    <flux:error name="age" />
                </flux:field>
            </div>

            {{-- Connections --}}
            <x-connections-card :connections="$this->allConnections" :sort-by="$connectionSortBy" :sort-dir="$connectionSortDir" />

            {{-- Notes --}}
            <div class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                <flux:field>
                    <flux:label>Notes</flux:label>
                    <flux:textarea wire:model="notes" rows="4" />
                </flux:field>
            </div>

            <div class="flex gap-3">
                <flux:button wire:click="save" variant="primary">Save NPC</flux:button>
                <flux:button href="{{ route('npcs.index') }}" variant="ghost" wire:navigate>Back</flux:button>
            </div>
        </div>

        {{-- Right column: UPP + Skills (1/3 width) --}}
        <div class="space-y-4">
            @include('livewire.shared.image-card', ['model' => $npc])

            @include('livewire.shared.upp-card')

            @include('livewire.shared.skills-card')
        </div>
    </div>

    {{-- Connection Modal --}}
    <flux:modal name="npc-connection-modal" class="md:w-80">
        <flux:heading>{{ $editingConnectionId ? 'Edit Connection' : 'Add Connection' }}</flux:heading>
        <div class="mt-4 space-y-4">
            <flux:field>
                <flux:label>Type</flux:label>
                <flux:select wire:model.live="connectionModalType" :disabled="(bool) $editingConnectionId">
                    <option value="character">Character</option>
                    <option value="org">Organization</option>
                </flux:select>
            </flux:field>

            @if ($connectionModalType === 'character')
                <flux:field>
                    <flux:label>Character</flux:label>
                    <flux:select wire:model="connectionModalCharacterId">
                        <option value="">Select character...</option>
                        @foreach($allCharacters as $character)
                            <option value="{{ $character->id }}">{{ $character->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="connectionModalCharacterId" />
                </flux:field>
                <flux:field>
                    <flux:label>Relationship</flux:label>
                    <flux:select wire:model="connectionModalCharacterRelType">
                        @foreach($npcRelationshipTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>
            @else
                <flux:field>
                    <flux:label>Organization</flux:label>
                    <flux:select wire:model="connectionModalOrgId">
                        <option value="">Select organization...</option>
                        @foreach($allOrgs as $org)
                            <option value="{{ $org->id }}">{{ $org->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="connectionModalOrgId" />
                </flux:field>
                <flux:field>
                    <flux:label>Relationship</flux:label>
                    <flux:select wire:model="connectionModalOrgRelType">
                        @foreach($orgRelationshipTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>
            @endif

            <flux:field>
                <flux:label>Notes</flux:label>
                <flux:textarea wire:model="connectionModalNotes" rows="2" />
            </flux:field>
        </div>
        <div class="mt-6 flex justify-end gap-2">
            <flux:modal.close><flux:button variant="ghost">Cancel</flux:button></flux:modal.close>
            <flux:button wire:click="saveConnection" variant="primary">Save</flux:button>
        </div>
    </flux:modal>
</div>
