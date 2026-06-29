<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center gap-3">
        <flux:button href="{{ route('organizations.index') }}" icon="arrow-left" variant="ghost" size="sm" wire:navigate />
        <x-entity-icon :model="$organization" class="h-10 w-10 rounded-full" />
        <flux:heading size="xl">{{ $organization->name }}</flux:heading>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        {{-- Main form (2/3 width) --}}
        <div class="space-y-8 lg:col-span-2">
            <div class="space-y-4 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                <flux:field>
                    <flux:label>Name @if(blank($name))<flux:badge color="red" size="sm">Required</flux:badge>@endif</flux:label>
                    <flux:input wire:model="name" />
                    <flux:error name="name" />
                </flux:field>

                <div class="grid grid-cols-2 gap-4">
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
                        <flux:select wire:model="baseOfOperationsPlanetId" placeholder="Select planet...">
                            <option value="">— none —</option>
                            @foreach($planets as $id => $label)
                                <option value="{{ $id }}">{{ $label }}</option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                </div>
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
                <flux:button wire:click="save" variant="primary">Save</flux:button>
                <flux:button href="{{ route('organizations.index') }}" variant="ghost" wire:navigate>Back</flux:button>
            </div>
        </div>

        {{-- Right column (1/3 width) --}}
        <div class="space-y-4">
            @include('livewire.shared.image-card', ['model' => $organization])

            <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="px-6 pt-6 pb-4">
                    <flux:heading size="lg">Summary</flux:heading>
                </div>
                <div class="flex items-center gap-3 px-6 py-2 bg-zinc-200 dark:bg-zinc-800/40">
                    <span class="flex-1 text-sm text-zinc-700 dark:text-zinc-300">Characters</span>
                    <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $this->characterConnections->count() }}</span>
                </div>
                <div class="flex items-center gap-3 px-6 py-2">
                    <span class="flex-1 text-sm text-zinc-700 dark:text-zinc-300">NPCs</span>
                    <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $this->npcConnections->count() }}</span>
                </div>
                <div class="flex items-center gap-3 px-6 py-2 bg-zinc-200 dark:bg-zinc-800/40">
                    <span class="flex-1 text-sm text-zinc-700 dark:text-zinc-300">Organizations</span>
                    <span class="text-sm font-semibold text-zinc-800 dark:text-zinc-200">{{ $this->orgConnections->count() }}</span>
                </div>
                <div class="pb-2"></div>
            </div>
        </div>
    </div>

    {{-- Connection Modal --}}
    <flux:modal name="org-connection-modal" class="md:w-80">
        <flux:heading>{{ $editingConnectionId ? 'Edit Connection' : 'Add Connection' }}</flux:heading>
        <div class="mt-4 space-y-4">
            <flux:field>
                <flux:label>Type</flux:label>
                <flux:select wire:model.live="connectionModalType" :disabled="(bool) $editingConnectionId">
                    <option value="character">Character (PC)</option>
                    <option value="npc">NPC</option>
                    <option value="org">Organization</option>
                </flux:select>
            </flux:field>

            @if ($connectionModalType === 'character')
                <flux:field>
                    <flux:label>Character <flux:badge color="red" size="sm">Required</flux:badge></flux:label>
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
                        @foreach($orgRelationshipTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>
            @elseif ($connectionModalType === 'npc')
                <flux:field>
                    <flux:label>NPC <flux:badge color="red" size="sm">Required</flux:badge></flux:label>
                    <flux:select wire:model="connectionModalNpcId">
                        <option value="">Select NPC...</option>
                        @foreach($allNpcs as $npc)
                            <option value="{{ $npc->id }}">{{ $npc->name }}</option>
                        @endforeach
                    </flux:select>
                    <flux:error name="connectionModalNpcId" />
                </flux:field>
                <flux:field>
                    <flux:label>Relationship</flux:label>
                    <flux:select wire:model="connectionModalNpcRelType">
                        @foreach($orgRelationshipTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </flux:select>
                </flux:field>
            @else
                <flux:field>
                    <flux:label>Organization <flux:badge color="red" size="sm">Required</flux:badge></flux:label>
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
