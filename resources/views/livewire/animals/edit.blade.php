<div class="flex h-full w-full flex-1 flex-col gap-6 p-6">
    {{-- Header --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <x-entity-icon :model="$animal" class="h-10 w-10 rounded-full" />
            <flux:heading size="xl">
                {{ $animal->name ?: 'New Animal' }}
                @if ($animal->parent)
                    <span class="text-base font-normal text-zinc-400">(variant of <a href="{{ route('animals.edit', $animal->parent) }}" class="hover:text-blue-500" wire:navigate>{{ $animal->parent->name }}</a>)</span>
                @endif
            </flux:heading>
        </div>
        <flux:button href="{{ route('animals.index') }}" variant="ghost" icon="arrow-left" wire:navigate>Animals</flux:button>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Left column --}}
        <div class="lg:col-span-2 space-y-6">
            <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="px-6 pt-6 pb-4">
                    <flux:heading size="lg">Identity</flux:heading>
                </div>
                <div class="space-y-4 px-6 pb-6">
                    <flux:field>
                        <flux:label>Name @if(blank($name))<flux:badge color="red" size="sm">Required</flux:badge>@endif</flux:label>
                        <flux:input wire:model="name" placeholder="Species name" />
                        <flux:error name="name" />
                    </flux:field>

                    <flux:field>
                        <flux:label>Native Planet</flux:label>
                        <livewire:components.planet-select wire:model="nativePlanetId" />
                    </flux:field>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Hits</flux:label>
                            <flux:input type="number" wire:model="hits" min="1" placeholder="e.g. 20" />
                        </flux:field>
                        <flux:field>
                            <flux:label>Speed (metres)</flux:label>
                            <flux:input type="number" wire:model="speed" min="1" placeholder="e.g. 6" />
                        </flux:field>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <flux:field>
                            <flux:label>Behavior Type</flux:label>
                            <flux:select wire:model.live="behaviorType" placeholder="Select type...">
                                <option value="">— none —</option>
                                @foreach($behaviorTypes as $type)
                                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                        <flux:field>
                            <flux:label>Behavior Subtype</flux:label>
                            <flux:select wire:model="behaviorSubtype" placeholder="Select subtype..." :disabled="!$behaviorType">
                                <option value="">— none —</option>
                                @foreach($behaviorSubtypes as $subtype)
                                    <option value="{{ $subtype->value }}">{{ $subtype->label() }}</option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                    </div>

                    <flux:field>
                        <flux:label>Notes</flux:label>
                        <flux:textarea wire:model="notes" rows="3" />
                    </flux:field>
                </div>
            </div>

            {{-- Skills --}}
            @include('livewire.shared.skills-card')

            {{-- Attacks --}}
            <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center justify-between px-6 pt-6 pb-4">
                    <flux:heading size="lg">Attacks</flux:heading>
                    <flux:button size="sm" icon="plus" wire:click="openAttackModal">Add</flux:button>
                </div>
                @if ($this->attacks->isEmpty())
                    <div class="px-6 pb-6">
                        <flux:text class="text-sm text-zinc-400">No attacks yet.</flux:text>
                    </div>
                @else
                    <div class="pb-4">
                        @foreach ($this->attacks as $attack)
                            <div class="flex items-center justify-between px-6 py-2 {{ $loop->odd ? 'bg-zinc-200 dark:bg-zinc-800/40' : '' }} hover:bg-zinc-100 dark:hover:bg-zinc-700/40">
                                <div>
                                    <span class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $attack->name }}</span>
                                    <span class="ml-2 font-mono text-sm text-blue-600 dark:text-blue-400">{{ $attack->damage }}</span>
                                    @if ($attack->attack_traits)
                                        <span class="ml-2 text-sm text-zinc-500 dark:text-zinc-400">{{ $attack->attack_traits }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <flux:button size="xs" icon="pencil" variant="ghost" wire:click="openAttackModal({{ $attack->id }})" />
                                    <flux:button size="xs" icon="trash" variant="ghost" wire:click="deleteAttack({{ $attack->id }})" wire:confirm="Delete this attack?" class="text-red-500" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Traits --}}
            <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center justify-between px-6 pt-6 pb-4">
                    <flux:heading size="lg">Traits</flux:heading>
                    <flux:button size="sm" icon="plus" wire:click="openTraitModal">Add</flux:button>
                </div>
                @if ($this->animalTraits->isEmpty())
                    <div class="px-6 pb-6">
                        <flux:text class="text-sm text-zinc-400">No traits yet.</flux:text>
                    </div>
                @else
                    <div class="pb-4">
                        @foreach ($this->animalTraits as $trait)
                            <div class="flex items-center justify-between px-6 py-2 {{ $loop->odd ? 'bg-zinc-200 dark:bg-zinc-800/40' : '' }} hover:bg-zinc-100 dark:hover:bg-zinc-700/40">
                                <div>
                                    <span class="text-sm font-medium text-zinc-800 dark:text-zinc-200">{{ $trait->name }}</span>
                                    @if ($trait->value)
                                        <span class="ml-2 font-mono text-sm text-blue-600 dark:text-blue-400">{{ $trait->value }}</span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2">
                                    <flux:button size="xs" icon="pencil" variant="ghost" wire:click="openTraitModal({{ $trait->id }})" />
                                    <flux:button size="xs" icon="trash" variant="ghost" wire:click="deleteTrait({{ $trait->id }})" wire:confirm="Delete this trait?" class="text-red-500" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Variants --}}
            <div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center justify-between px-6 pt-6 pb-4">
                    <flux:heading size="lg">Variants</flux:heading>
                    <flux:button size="sm" icon="plus" href="{{ route('animals.create', ['parent' => $animal->id]) }}" wire:navigate>Add Variant</flux:button>
                </div>
                @if ($this->variants->isEmpty())
                    <div class="px-6 pb-6">
                        <flux:text class="text-sm text-zinc-400">No variants yet.</flux:text>
                    </div>
                @else
                    <table class="w-full text-sm">
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700">
                            @foreach ($this->variants as $variant)
                                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
                                    <td class="px-6 py-3">
                                        <a href="{{ route('animals.edit', $variant) }}" class="inline-flex items-center gap-2 font-medium text-zinc-800 hover:text-blue-600 dark:text-zinc-200" wire:navigate>
                                            <x-entity-icon :model="$variant" />
                                            {{ $variant->name }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $variant->nativePlanet?->display_label ?? '—' }}</td>
                                    <td class="px-4 py-3 text-zinc-500 dark:text-zinc-400">{{ $variant->hits ? 'Hits ' . $variant->hits : '' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        {{-- Right column --}}
        <div class="space-y-6">
            @include('livewire.shared.image-card', ['model' => $animal])
        </div>
    </div>

    <div class="flex justify-end">
        <flux:button wire:click="save" variant="primary">Save Animal</flux:button>
    </div>

    {{-- Skill modal (from shared skills-card) --}}
    <flux:modal wire:model="skillModalOpen" class="md:w-80">
        <flux:heading>{{ $editingSkillId ? 'Edit Skill' : 'Add Skill' }}</flux:heading>
        <div class="mt-4 space-y-4">
            <flux:field>
                <flux:label>Skill</flux:label>
                <flux:select wire:model="skillModalName">
                    <option value="">Select skill...</option>
                    @foreach($mgt2Skills as $skill)
                        <option value="{{ $skill }}">{{ $skill }}</option>
                    @endforeach
                </flux:select>
                <flux:error name="skillModalName" />
            </flux:field>
            <flux:field>
                <flux:label>Level</flux:label>
                <flux:input type="number" wire:model="skillModalLevel" min="0" max="6" />
                <flux:error name="skillModalLevel" />
            </flux:field>
        </div>
        <div class="mt-6 flex justify-end gap-2">
            <flux:modal.close><flux:button variant="ghost">Cancel</flux:button></flux:modal.close>
            @if (!$editingSkillId)
                <flux:button wire:click="saveSkill(true)">Save and New</flux:button>
            @endif
            <flux:button wire:click="saveSkill" variant="primary">Save and Close</flux:button>
        </div>
    </flux:modal>

    {{-- Attack modal --}}
    <flux:modal wire:model="attackModalOpen" class="md:w-80">
        <flux:heading>{{ $editingAttackId ? 'Edit Attack' : 'Add Attack' }}</flux:heading>
        <div class="mt-4 space-y-4">
            <flux:field>
                <flux:label>Name @if(blank($attackModalName))<flux:badge color="red" size="sm">Required</flux:badge>@endif</flux:label>
                <flux:input wire:model="attackModalName" placeholder="e.g. Claws, Teeth" />
                <flux:error name="attackModalName" />
            </flux:field>
            <flux:field>
                <flux:label>Damage @if(blank($attackModalDamage))<flux:badge color="red" size="sm">Required</flux:badge>@endif</flux:label>
                <flux:input wire:model="attackModalDamage" placeholder="e.g. 2D+2, 1D" />
                <flux:error name="attackModalDamage" />
            </flux:field>
            <flux:field>
                <flux:label>Traits</flux:label>
                <flux:input wire:model="attackModalTraits" placeholder="e.g. AP 2, Stun" />
            </flux:field>
        </div>
        <div class="mt-6 flex justify-end gap-2">
            <flux:modal.close><flux:button variant="ghost">Cancel</flux:button></flux:modal.close>
            <flux:button wire:click="saveAttack" variant="primary">Save</flux:button>
        </div>
    </flux:modal>

    {{-- Trait modal --}}
    <flux:modal wire:model="traitModalOpen" class="md:w-80">
        <flux:heading>{{ $editingTraitId ? 'Edit Trait' : 'Add Trait' }}</flux:heading>
        <div class="mt-4 space-y-4">
            <flux:field>
                <flux:label>Name @if(blank($traitModalName))<flux:badge color="red" size="sm">Required</flux:badge>@endif</flux:label>
                <flux:input wire:model="traitModalName" placeholder="e.g. Armor, Large, Heightened Senses" />
                <flux:error name="traitModalName" />
            </flux:field>
            <flux:field>
                <flux:label>Value</flux:label>
                <flux:input wire:model="traitModalValue" placeholder="e.g. 2, +2" />
            </flux:field>
        </div>
        <div class="mt-6 flex justify-end gap-2">
            <flux:modal.close><flux:button variant="ghost">Cancel</flux:button></flux:modal.close>
            <flux:button wire:click="saveTrait" variant="primary">Save</flux:button>
        </div>
    </flux:modal>
</div>
