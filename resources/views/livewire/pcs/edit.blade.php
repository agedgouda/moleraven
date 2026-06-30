<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    <div class="flex items-center gap-3">
        <flux:button href="{{ route('pcs.index') }}" icon="arrow-left" variant="ghost" size="sm" wire:navigate />
        <x-entity-icon :model="$character" class="h-10 w-10 rounded-full" />
        <div>
            <flux:heading size="xl">{{ $character->name }}</flux:heading>
            <span class="font-mono text-sm tracking-widest text-zinc-400">{{ $character->uppString() }}</span>
        </div>
        <div class="ml-auto flex gap-2">
            <flux:button href="{{ route('pcs.index') }}" variant="ghost" wire:navigate>Back</flux:button>
            <flux:button wire:click="save" variant="primary">Save PC</flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        {{-- Left column: core form (2/3) --}}
        <div class="space-y-6 xl:col-span-2">

            {{-- Identity --}}
            <div class="space-y-4 rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                <div class="flex justify-end">
                    <label for="edit_is_current" class="flex cursor-pointer items-center gap-2 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                        Current PC
                        <flux:checkbox wire:model="isCurrent" id="edit_is_current" />
                    </label>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <flux:field>
                        <flux:label>Name</flux:label>
                        <flux:input wire:model="name" />
                        <flux:error name="name" />
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
            </div>

            {{-- Connections / Background / Inventory tabs --}}
            <div class="rounded-xl border border-zinc-200 dark:border-zinc-700" x-data="{ tab: 'connections' }">
                <div class="border-b border-zinc-200 px-4 pb-1 dark:border-zinc-700">
                    <flux:navbar class="pb-2">
                        <flux:navbar.item :current="false" x-on:click="tab = 'connections'" x-bind:data-current="tab === 'connections' ? '' : null">Connections</flux:navbar.item>
                        <flux:navbar.item :current="false" x-on:click="tab = 'background'" x-bind:data-current="tab === 'background' ? '' : null">Background</flux:navbar.item>
                        <flux:navbar.item :current="false" x-on:click="tab = 'inventory'" x-bind:data-current="tab === 'inventory' ? '' : null">Inventory</flux:navbar.item>
                    </flux:navbar>
                </div>

                    {{-- Connections --}}
                    <div x-show="tab === 'connections'" class="p-6">
                        <x-connections-card :connections="$this->allConnections" :card="false" :sort-by="$connectionSortBy" :sort-dir="$connectionSortDir" />
                    </div>

                    {{-- Background --}}
                    <div x-show="tab === 'background'" class="p-6">
                        <div class="mb-4 flex justify-end">
                            <flux:button size="sm" icon="plus" wire:click="openCareerModal">Add Term</flux:button>
                        </div>

                        <div class="mb-4 grid grid-cols-2 gap-4">
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
                                <flux:label>Age</flux:label>
                                <flux:input type="number" wire:model="age" min="1" max="150" />
                                <flux:error name="age" />
                            </flux:field>
                        </div>

                        @if ($this->careerTerms->isEmpty())
                            <flux:text class="text-sm text-zinc-400">No background yet.</flux:text>
                        @else
                            <div class="overflow-hidden rounded-lg border border-zinc-100 dark:border-zinc-700">
                                <table class="w-full text-sm">
                                    <thead class="bg-zinc-50 dark:bg-zinc-800">
                                        <tr>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-zinc-500">Term</th>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-zinc-500">Career</th>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-zinc-500">Assignment</th>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-zinc-500">Rank</th>
                                            <th class="px-3 py-2 text-left text-xs font-semibold text-zinc-500">Title</th>
                                            <th class="px-3 py-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                                        @foreach ($this->careerTerms as $term)
                                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                                <td class="px-3 py-2 font-mono">{{ $term->term }}</td>
                                                <td class="px-3 py-2">{{ $term->career }}</td>
                                                <td class="px-3 py-2 text-zinc-500">{{ $term->assignment ?? '—' }}</td>
                                                <td class="px-3 py-2">{{ $term->rank }}</td>
                                                <td class="px-3 py-2 text-zinc-500">{{ $term->rank_title ?? '—' }}</td>
                                                <td class="px-3 py-2 text-right">
                                                    <div class="flex justify-end gap-1">
                                                        <flux:button size="xs" icon="pencil" variant="ghost" wire:click="openCareerModal({{ $term->id }})" />
                                                        <flux:button size="xs" icon="trash" variant="ghost" wire:click="deleteCareerTerm({{ $term->id }})" wire:confirm="Delete this background term?" class="text-red-500" />
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>

                    {{-- Inventory --}}
                    <div x-show="tab === 'inventory'" class="p-6">
                        <div class="mb-4 flex justify-end">
                            <flux:button size="sm" icon="plus" wire:click="openInventoryModal">Add Item</flux:button>
                        </div>

                        <flux:field class="mb-4">
                            <flux:label>Credits</flux:label>
                            <flux:input type="number" wire:model="credits" min="0" />
                            <flux:error name="credits" />
                        </flux:field>

                        @if ($this->inventoryItems->isEmpty())
                            <flux:text class="text-sm text-zinc-400">No inventory items.</flux:text>
                        @else
                            <div class="space-y-2">
                                @foreach ($this->inventoryItems as $item)
                                    <div class="flex items-center justify-between rounded-lg border border-zinc-100 px-3 py-2 dark:border-zinc-700">
                                        <div>
                                            <span class="font-medium text-zinc-800 dark:text-zinc-200">{{ $item->name }}</span>
                                            <span class="ml-2 text-sm text-zinc-400">× {{ $item->quantity }}</span>
                                            @if ($item->description)
                                                <div class="text-xs text-zinc-400">{{ Str::limit($item->description, 60) }}</div>
                                            @endif
                                        </div>
                                        <div class="flex gap-1">
                                            <flux:button size="xs" icon="pencil" variant="ghost" wire:click="openInventoryModal({{ $item->id }})" />
                                            <flux:button size="xs" icon="trash" variant="ghost" wire:click="deleteInventoryItem({{ $item->id }})" wire:confirm="Delete this item?" class="text-red-500" />
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
            </div>

            {{-- Notes --}}
            <div class="rounded-xl border border-zinc-200 p-6 dark:border-zinc-700">
                <flux:field>
                    <flux:label>Notes</flux:label>
                    <flux:textarea wire:model="notes" rows="4" />
                </flux:field>
            </div>
        </div>

        {{-- Right column: UPP, skills, NPCs, orgs (1/3) --}}
        <div class="space-y-6">

            @include('livewire.shared.image-card', ['model' => $character])

            @include('livewire.shared.upp-card')

            @include('livewire.shared.skills-card')


        </div>
    </div>

    {{-- Career Modal --}}
    <flux:modal name="career-modal" class="md:w-96">
        <flux:heading>{{ $editingCareerTermId ? 'Edit Background Term' : 'Add Background Term' }}</flux:heading>
        <div class="mt-4 grid grid-cols-2 gap-4">
            <flux:field class="col-span-2">
                <flux:label>Career <flux:badge color="red" size="sm">Required</flux:badge></flux:label>
                <flux:input wire:model="careerModalCareer" placeholder="Navy, Army, Scout..." />
                <flux:error name="careerModalCareer" />
            </flux:field>
            <flux:field>
                <flux:label>Assignment</flux:label>
                <flux:input wire:model="careerModalAssignment" placeholder="e.g. Flight" />
            </flux:field>
            <flux:field>
                <flux:label>Term #</flux:label>
                <flux:input type="number" wire:model="careerModalTerm" min="1" />
                <flux:error name="careerModalTerm" />
            </flux:field>
            <flux:field>
                <flux:label>Rank</flux:label>
                <flux:input type="number" wire:model="careerModalRank" min="0" max="6" />
                <flux:error name="careerModalRank" />
            </flux:field>
            <flux:field>
                <flux:label>Rank Title</flux:label>
                <flux:input wire:model="careerModalRankTitle" placeholder="e.g. Lieutenant" />
            </flux:field>
            <flux:field class="col-span-2">
                <flux:label>Notes</flux:label>
                <flux:textarea wire:model="careerModalNotes" rows="2" />
            </flux:field>
        </div>
        <div class="mt-6 flex justify-end gap-2">
            <flux:modal.close><flux:button variant="ghost">Cancel</flux:button></flux:modal.close>
            <flux:button wire:click="saveCareerTerm" variant="primary">Save</flux:button>
        </div>
    </flux:modal>

    {{-- Inventory Modal --}}
    <flux:modal name="inventory-modal" class="md:w-80">
        <flux:heading>{{ $editingInventoryId ? 'Edit Item' : 'Add Item' }}</flux:heading>
        <div class="mt-4 space-y-4">
            <flux:field>
                <flux:label>Item Name <flux:badge color="red" size="sm">Required</flux:badge></flux:label>
                <flux:input wire:model="inventoryModalName" placeholder="e.g. Laser Pistol" />
                <flux:error name="inventoryModalName" />
            </flux:field>
            <flux:field>
                <flux:label>Quantity</flux:label>
                <flux:input type="number" wire:model="inventoryModalQuantity" min="1" />
                <flux:error name="inventoryModalQuantity" />
            </flux:field>
            <flux:field>
                <flux:label>Description</flux:label>
                <flux:textarea wire:model="inventoryModalDescription" rows="2" />
            </flux:field>
        </div>
        <div class="mt-6 flex justify-end gap-2">
            <flux:modal.close><flux:button variant="ghost">Cancel</flux:button></flux:modal.close>
            <flux:button wire:click="saveInventoryItem" variant="primary">Save</flux:button>
        </div>
    </flux:modal>

    {{-- Connection Modal --}}
    <flux:modal name="connection-modal" class="md:w-80">
        <flux:heading>{{ $editingConnectionId ? 'Edit Connection' : 'Add Connection' }}</flux:heading>
        <div class="mt-4 space-y-4">
            <flux:field>
                <flux:label>Type</flux:label>
                <flux:select wire:model.live="connectionModalType" :disabled="(bool) $editingConnectionId">
                    <option value="npc">NPC</option>
                    <option value="org">Organization</option>
                </flux:select>
            </flux:field>

            @if ($connectionModalType === 'npc')
                <flux:field>
                    <flux:label>NPC</flux:label>
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
