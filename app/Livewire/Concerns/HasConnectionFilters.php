<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Collection;

trait HasConnectionFilters
{
    public string $connectionSearch = '';

    public string $connectionSortBy = 'name';

    public string $connectionSortDir = 'asc';

    public function sortConnections(string $by): void
    {
        if ($this->connectionSortBy === $by) {
            $this->connectionSortDir = $this->connectionSortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->connectionSortBy = $by;
            $this->connectionSortDir = 'asc';
        }

        unset($this->allConnections);
    }

    public function updatedConnectionSearch(): void
    {
        unset($this->allConnections);
    }

    protected function applyConnectionFilters(Collection $connections): Collection
    {
        if ($this->connectionSearch !== '') {
            $search = strtolower($this->connectionSearch);
            $connections = $connections->filter(
                fn ($item) => str_contains(strtolower($item['name']), $search)
                    || str_contains(strtolower($item['label']), $search)
                    || str_contains(strtolower($item['relationshipType']->label()), $search)
                    || str_contains(strtolower($item['notes'] ?? ''), $search)
            );
        }

        $connections = $connections->sortBy(fn ($item) => match ($this->connectionSortBy) {
            'type' => strtolower($item['label']),
            'relation' => strtolower($item['relationshipType']->label()),
            default => strtolower($item['name']),
        });

        if ($this->connectionSortDir === 'desc') {
            $connections = $connections->reverse();
        }

        return $connections->values();
    }
}
