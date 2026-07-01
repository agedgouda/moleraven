<?php

namespace App\Livewire\DiaryEntries;

use App\Models\DiaryEntry;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Edit Diary Entry'])]
#[Title('Edit Diary Entry')]
class Edit extends Component
{
    public DiaryEntry $diaryEntry;

    #[Url(as: 'return_to')]
    public string $returnTo = '';

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

    public function mount(DiaryEntry $diaryEntry): void
    {
        $this->diaryEntry = $diaryEntry;
        $this->entryDate = $diaryEntry->entry_date ?? '';
        $this->entry = $diaryEntry->entry;
        $this->npcIds = $diaryEntry->npcs()->pluck('npcs.id')->toArray();
        $this->organizationIds = $diaryEntry->organizations()->pluck('organizations.id')->toArray();
        $this->animalIds = $diaryEntry->animals()->pluck('animals.id')->toArray();
        $this->planetIds = $diaryEntry->planets()->pluck('planets.id')->toArray();

        if ($this->returnTo === '') {
            $this->returnTo = url()->previous(route('home', ['entry_id' => $diaryEntry->id]));
        }
    }

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

        $this->diaryEntry->update([
            'entry_date' => $this->entryDate ?: null,
            'entry' => $this->entry ?: null,
        ]);

        $this->diaryEntry->npcs()->sync($this->npcIds);
        $this->diaryEntry->organizations()->sync($this->organizationIds);
        $this->diaryEntry->animals()->sync($this->animalIds);
        $this->diaryEntry->planets()->sync($this->planetIds);

        $back = $this->returnTo ?: route('home', ['entry_id' => $this->diaryEntry->id]);
        $this->redirect($back, navigate: true);
    }

    public function render(): View
    {
        return view('livewire.diary-entries.edit');
    }
}
