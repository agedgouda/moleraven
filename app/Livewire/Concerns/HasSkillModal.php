<?php

namespace App\Livewire\Concerns;

use Flux\Flux;
use Illuminate\Database\Eloquent\Model;

trait HasSkillModal
{
    public bool $skillModalOpen = false;

    public ?int $editingSkillId = null;

    public string $skillModalName = '';

    public int $skillModalLevel = 0;

    abstract protected function skillable(): Model;

    public function openSkillModal(?int $skillId = null): void
    {
        if ($skillId) {
            $skill = $this->skillable()->skills()->findOrFail($skillId);
            $this->editingSkillId = $skillId;
            $this->skillModalName = $skill->name;
            $this->skillModalLevel = $skill->level;
        } else {
            $this->editingSkillId = null;
            $this->skillModalName = '';
            $this->skillModalLevel = 0;
        }

        $this->skillModalOpen = true;
    }

    public function saveSkill(bool $andNew = false): void
    {
        $this->validate([
            'skillModalName' => 'required|string|max:255',
            'skillModalLevel' => 'required|integer|min:0|max:6',
        ]);

        if ($this->editingSkillId) {
            $this->skillable()->skills()->findOrFail($this->editingSkillId)->update([
                'name' => $this->skillModalName,
                'level' => $this->skillModalLevel,
            ]);
        } else {
            $this->skillable()->skills()->create([
                'name' => $this->skillModalName,
                'level' => $this->skillModalLevel,
            ]);
        }

        unset($this->skills);
        Flux::toast('Skill saved.');

        if ($andNew) {
            $this->editingSkillId = null;
            $this->skillModalName = '';
            $this->skillModalLevel = 0;
        } else {
            $this->skillModalOpen = false;
        }
    }

    public function deleteSkill(int $skillId): void
    {
        $this->skillable()->skills()->findOrFail($skillId)->delete();
        unset($this->skills);
    }
}
