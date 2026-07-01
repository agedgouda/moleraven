<?php

namespace App\Livewire\Home;

use App\Models\Animal;
use App\Models\DiaryEntry;
use App\Models\Npc;
use App\Models\Organization;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

/**
 * @property-read DiaryEntry|null $latestEntry
 * @property-read int $totalEntries
 * @property-read Collection<int, array{model: Npc|Organization|Animal, name: string, type: string, route: string}> $contacts
 */
#[Layout('layouts.public', ['title' => 'Latest Entry'])]
#[Title('Latest Entry')]
class Index extends Component
{
    public int $offset = 0;

    #[Url(as: 'entry_id')]
    public ?int $entryId = null;

    public string $contactSearch = '';

    public string $contactSortBy = 'name';

    public string $contactSortDir = 'asc';

    public function goToPrevious(): void
    {
        $this->entryId = null;
        $this->offset++;
        unset($this->latestEntry, $this->contacts);
    }

    public function goToNext(): void
    {
        if ($this->offset > 0) {
            $this->entryId = null;
            $this->offset--;
            unset($this->latestEntry, $this->contacts);
        }
    }

    public function sortContacts(string $by): void
    {
        if ($this->contactSortBy === $by) {
            $this->contactSortDir = $this->contactSortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->contactSortBy = $by;
            $this->contactSortDir = 'asc';
        }

        unset($this->contacts);
    }

    public function updatedContactSearch(): void
    {
        unset($this->contacts);
    }

    #[Computed]
    public function latestEntry(): ?DiaryEntry
    {
        if ($this->entryId !== null) {
            return DiaryEntry::with(['character', 'npcs', 'organizations', 'animals', 'planets'])
                ->whereHas('character', fn ($q) => $q->where('status', 'active'))
                ->find($this->entryId);
        }

        return DiaryEntry::with(['character', 'npcs', 'organizations', 'animals', 'planets'])
            ->whereHas('character', fn ($q) => $q->where('status', 'active'))
            ->orderByDesc('entry_date')
            ->orderByDesc('created_at')
            ->skip($this->offset)
            ->first();
    }

    #[Computed]
    public function totalEntries(): int
    {
        return DiaryEntry::whereHas('character', fn ($q) => $q->where('status', 'active'))->count();
    }

    /** @return Collection<int, array{model: Npc|Organization|Animal, name: string, type: string, route: string}> */
    #[Computed]
    public function contacts(): Collection
    {
        $entry = $this->latestEntry;

        if (! $entry) {
            return collect();
        }

        $all = collect()
            ->concat($entry->npcs->map(fn ($n) => ['model' => $n, 'name' => $n->name, 'type' => 'NPC', 'route' => route('npcs.show', $n)]))
            ->concat($entry->organizations->map(fn ($o) => ['model' => $o, 'name' => $o->name, 'type' => 'Organization', 'route' => route('organizations.show', $o)]))
            ->concat($entry->animals->map(fn ($a) => ['model' => $a, 'name' => $a->name, 'type' => 'Animal', 'route' => route('animals.show', $a)]));

        if ($this->contactSearch !== '') {
            $search = strtolower($this->contactSearch);
            $all = $all->filter(
                fn ($item) => str_contains(strtolower($item['name']), $search)
                    || str_contains(strtolower($item['type']), $search)
            );
        }

        $all = $all->sortBy(fn ($item) => match ($this->contactSortBy) {
            'type' => strtolower($item['type']),
            default => strtolower($item['name']),
        });

        if ($this->contactSortDir === 'desc') {
            $all = $all->reverse();
        }

        return $all->values();
    }

    public function render(): View
    {
        return view('livewire.home.index');
    }
}
