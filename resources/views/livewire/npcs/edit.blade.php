<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center gap-3">
        <flux:button href="{{ route('npcs.index') }}" icon="arrow-left" variant="ghost" size="sm" wire:navigate />
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
                    <flux:label>Name <flux:badge color="red" size="sm">Required</flux:badge></flux:label>
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
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center justify-between px-6 pt-6 pb-4">
                    <flux:heading size="lg">Connections</flux:heading>
                    <flux:button size="sm" icon="plus" wire:click="openConnectionModal">Add</flux:button>
                </div>

                @php
                    $allConnections = $this->characterConnections->map(fn($c) => ['type' => 'character', 'conn' => $c])
                        ->concat($this->orgConnections->map(fn($c) => ['type' => 'org', 'conn' => $c]))
                        ->sortBy(fn($item) => $item['type'] === 'character' ? $item['conn']->character->name : $item['conn']->organization->name);
                @endphp

                @if ($allConnections->isEmpty())
                    <div class="px-6 pb-6">
                        <flux:text class="text-sm text-zinc-400">No connections yet.</flux:text>
                    </div>
                @else
                    <div class="space-y-2 px-6 pb-6">
                        @foreach ($allConnections as $item)
                            @php $type = $item['type']; $conn = $item['conn']; $rt = $conn->relationship_type; @endphp
                            <div class="flex items-start justify-between rounded-lg border border-zinc-100 px-3 py-2 dark:border-zinc-700">
                                <div>
                                    @if ($type === 'character')
                                        <a href="{{ route('pcs.edit', $conn->character) }}" class="font-medium text-zinc-800 hover:text-blue-600 dark:text-zinc-200" wire:navigate>{{ $conn->character->name }}</a>
                                        <span class="ml-1 text-xs text-zinc-400">PC</span>
                                    @else
                                        <a href="{{ route('organizations.edit', $conn->organization) }}" class="font-medium text-zinc-800 hover:text-blue-600 dark:text-zinc-200" wire:navigate>{{ $conn->organization->name }}</a>
                                        <span class="ml-1 text-xs text-zinc-400">Org</span>
                                    @endif
                                    <span class="ml-2 rounded-full px-1.5 py-0.5 text-xs font-semibold
                                        @if($rt->color() === 'success') bg-green-100 text-green-800
                                        @elseif($rt->color() === 'info') bg-blue-100 text-blue-800
                                        @elseif($rt->color() === 'warning') bg-amber-100 text-amber-800
                                        @elseif($rt->color() === 'primary') bg-purple-100 text-purple-800
                                        @elseif($rt->color() === 'danger') bg-red-100 text-red-800
                                        @else bg-zinc-100 text-zinc-700 @endif">
                                        {{ $rt->label() }}
                                    </span>
                                    @if ($conn->notes)
                                        <div class="text-xs text-zinc-400">{{ Str::limit($conn->notes, 50) }}</div>
                                    @endif
                                </div>
                                <div class="flex shrink-0 gap-1">
                                    <flux:button size="xs" icon="pencil" variant="ghost" wire:click="openConnectionModal({{ $conn->id }}, '{{ $type }}')" />
                                    <flux:button size="xs" icon="trash" variant="ghost" wire:click="deleteConnection({{ $conn->id }}, '{{ $type }}')" wire:confirm="Remove this connection?" class="text-red-500" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

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
                        @foreach($npcRelationshipTypes as $type)
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
