<?php

namespace App\Livewire\Organizations;

use App\Models\Organization;
use Illuminate\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app', ['title' => 'Organizations'])]
#[Title('Organizations')]
class Index extends Component
{
    public string $search = '';

    public function deleteOrganization(int $id): void
    {
        Organization::findOrFail($id)->delete();
    }

    public function render(): View
    {
        $organizations = Organization::query()
            ->with('baseOfOperations')
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%")
                ->orWhere('type', 'like', "%{$this->search}%"))
            ->orderBy('name')
            ->get();

        return view('livewire.organizations.index', compact('organizations'));
    }
}
