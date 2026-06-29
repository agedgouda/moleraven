<div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
    <div class="flex items-center justify-between px-6 pt-6 pb-4">
        <flux:heading size="lg">Skills</flux:heading>
        <flux:button size="sm" icon="plus" wire:click="openSkillModal">Add</flux:button>
    </div>

    @if ($this->skills->isEmpty())
        <div class="px-6" style="padding-bottom: 1.5rem">
            <flux:text class="text-sm text-zinc-400">No skills yet.</flux:text>
        </div>
    @else
        <div style="padding-bottom: 1rem">
            @foreach ($this->skills as $skill)
                <div class="flex items-center justify-between px-6 py-2 {{ $loop->odd ? 'bg-zinc-200 dark:bg-zinc-800/40' : '' }} hover:bg-zinc-100 dark:hover:bg-zinc-700/40">
                    <span class="text-sm text-zinc-800 dark:text-zinc-200">{{ $skill->name }}</span>
                    <div class="flex items-center gap-2">
                        <span class="font-mono text-sm font-semibold text-blue-600 dark:text-blue-400">{{ $skill->level }}</span>
                        <flux:button size="xs" icon="pencil" variant="ghost" wire:click="openSkillModal({{ $skill->id }})" />
                        <flux:button size="xs" icon="trash" variant="ghost" wire:click="deleteSkill({{ $skill->id }})" wire:confirm="Delete this skill?" class="text-red-500" />
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<flux:modal name="skill-modal" class="md:w-80">
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
        <flux:button wire:click="saveSkill" variant="primary">Save</flux:button>
    </div>
</flux:modal>
