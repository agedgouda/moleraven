<?php

namespace App\Livewire\DiaryEntries;

use App\Models\Character;
use App\Models\DiaryEntry;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'New Diary Entry'])]
#[Title('New Diary Entry')]
class Create extends Component
{
    public Character $character;

    public string $entryDate = '';

    public ?string $entry = null;

    /** @var array<int> */
    public array $npcIds = [];

    /** @var array<int> */
    public array $organizationIds = [];

    /** @var array<int> */
    public array $animalIds = [];

    /** @var array<int> */
    public array $planetIds = [];

    public function save(): void
    {
        $this->validate([
            'entryDate' => 'nullable|string|max:20',
            'entry' => 'nullable|string',
            'npcIds.*' => 'integer|exists:npcs,id',
            'organizationIds.*' => 'integer|exists:organizations,id',
            'animalIds.*' => 'integer|exists:animals,id',
            'planetIds.*' => 'integer|exists:planets,id',
        ]);

        $diaryEntry = DiaryEntry::create([
            'character_id' => $this->character->id,
            'entry_date' => $this->entryDate ?: null,
            'entry' => $this->entry ?: null,
        ]);

        $diaryEntry->npcs()->sync($this->npcIds);
        $diaryEntry->organizations()->sync($this->organizationIds);
        $diaryEntry->animals()->sync($this->animalIds);
        $diaryEntry->planets()->sync($this->planetIds);

        $this->redirect(route('pcs.edit', $this->character), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.diary-entries.create');
    }
}
