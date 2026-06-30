<?php

namespace App\Livewire\Animals;

use App\Enums\BehaviorSubtype;
use App\Enums\BehaviorType;
use App\Livewire\Concerns\HasSkillModal;
use App\Models\Animal;
use App\Models\AnimalAttack;
use App\Models\AnimalTrait;
use App\Models\Planet;
use App\Support\Mgt2;
use Flux\Flux;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app', ['title' => 'Edit Animal'])]
#[Title('Edit Animal')]
class Edit extends Component
{
    use HasSkillModal, WithFileUploads;

    public Animal $animal;

    public $imageUpload;

    public string $name = '';

    public ?int $nativePlanetId = null;

    public ?int $hits = null;

    public ?int $speed = null;

    public ?string $behaviorType = null;

    public ?string $behaviorSubtype = null;

    public ?string $notes = null;

    // --- Attack modal ---
    public bool $attackModalOpen = false;

    public ?int $editingAttackId = null;

    public string $attackModalName = '';

    public string $attackModalDamage = '';

    public string $attackModalTraits = '';

    // --- Trait modal ---
    public bool $traitModalOpen = false;

    public ?int $editingTraitId = null;

    public string $traitModalName = '';

    public string $traitModalValue = '';

    #[Computed]
    public function skills(): Collection
    {
        return $this->animal->skills()->orderBy('name')->get();
    }

    #[Computed]
    public function attacks(): Collection
    {
        return $this->animal->attacks()->orderBy('name')->get();
    }

    #[Computed]
    public function animalTraits(): Collection
    {
        return $this->animal->traits()->orderBy('name')->get();
    }

    #[Computed]
    public function variants(): Collection
    {
        return $this->animal->variants()->with('nativePlanet')->orderBy('name')->get();
    }

    public function mount(Animal $animal): void
    {
        $this->animal = $animal;
        $this->name = $animal->name;
        $this->nativePlanetId = $animal->native_planet_id;
        $this->hits = $animal->hits;
        $this->speed = $animal->speed;
        $this->behaviorType = $animal->behavior_type?->value;
        $this->behaviorSubtype = $animal->behavior_subtype?->value;
        $this->notes = $animal->notes ?? '';
    }

    public function save(): void
    {
        $this->validate(['name' => 'required|string|max:255']);

        $this->animal->update([
            'name' => $this->name,
            'native_planet_id' => $this->nativePlanetId,
            'hits' => $this->hits,
            'speed' => $this->speed,
            'behavior_type' => $this->behaviorType,
            'behavior_subtype' => $this->behaviorSubtype,
            'notes' => $this->notes ?: null,
        ]);

        Flux::toast('Animal saved.');
    }

    public function updatedBehaviorType(): void
    {
        $this->behaviorSubtype = null;
    }

    protected function skillable(): Animal
    {
        return $this->animal;
    }

    // --- Image ---

    public function updatedImageUpload(): void
    {
        $this->validate(['imageUpload' => 'image|max:4096']);
        if ($this->animal->image_path) {
            Storage::disk('public')->delete($this->animal->image_path);
        }
        $path = $this->imageUpload->store('animals', 'public');
        $this->animal->update(['image_path' => $path]);
    }

    public function deleteImage(): void
    {
        if ($this->animal->image_path) {
            Storage::disk('public')->delete($this->animal->image_path);
            $this->animal->update(['image_path' => null]);
        }
    }

    // --- Attack modal ---

    public function openAttackModal(?int $attackId = null): void
    {
        if ($attackId) {
            $attack = AnimalAttack::findOrFail($attackId);
            $this->editingAttackId = $attackId;
            $this->attackModalName = $attack->name;
            $this->attackModalDamage = $attack->damage;
            $this->attackModalTraits = $attack->attack_traits ?? '';
        } else {
            $this->editingAttackId = null;
            $this->attackModalName = '';
            $this->attackModalDamage = '';
            $this->attackModalTraits = '';
        }
        $this->attackModalOpen = true;
    }

    public function saveAttack(): void
    {
        $this->validate([
            'attackModalName' => 'required|string|max:255',
            'attackModalDamage' => 'required|string|max:50',
            'attackModalTraits' => 'nullable|string|max:255',
        ]);

        $data = [
            'name' => $this->attackModalName,
            'damage' => $this->attackModalDamage,
            'attack_traits' => $this->attackModalTraits ?: null,
        ];

        if ($this->editingAttackId) {
            AnimalAttack::findOrFail($this->editingAttackId)->update($data);
        } else {
            $this->animal->attacks()->create($data);
        }

        unset($this->attacks);
        $this->attackModalOpen = false;
        Flux::toast('Attack saved.');
    }

    public function deleteAttack(int $attackId): void
    {
        AnimalAttack::findOrFail($attackId)->delete();
        unset($this->attacks);
    }

    // --- Trait modal ---

    public function openTraitModal(?int $traitId = null): void
    {
        if ($traitId) {
            $trait = AnimalTrait::findOrFail($traitId);
            $this->editingTraitId = $traitId;
            $this->traitModalName = $trait->name;
            $this->traitModalValue = $trait->value ?? '';
        } else {
            $this->editingTraitId = null;
            $this->traitModalName = '';
            $this->traitModalValue = '';
        }
        $this->traitModalOpen = true;
    }

    public function saveTrait(): void
    {
        $this->validate([
            'traitModalName' => 'required|string|max:255',
            'traitModalValue' => 'nullable|string|max:100',
        ]);

        $data = [
            'name' => $this->traitModalName,
            'value' => $this->traitModalValue ?: null,
        ];

        if ($this->editingTraitId) {
            AnimalTrait::findOrFail($this->editingTraitId)->update($data);
        } else {
            $this->animal->traits()->create($data);
        }

        unset($this->animalTraits);
        $this->traitModalOpen = false;
        Flux::toast('Trait saved.');
    }

    public function deleteTrait(int $traitId): void
    {
        AnimalTrait::findOrFail($traitId)->delete();
        unset($this->animalTraits);
    }

    public function render(): View
    {
        $planets = Planet::orderBy('sector')->orderBy('hex')->get()->pluck('display_label', 'id');
        $mgt2Skills = Mgt2::SKILLS;
        $behaviorTypes = BehaviorType::cases();
        $behaviorSubtypes = $this->behaviorType
            ? BehaviorSubtype::forType(BehaviorType::from($this->behaviorType))
            : [];

        return view('livewire.animals.edit', compact('planets', 'mgt2Skills', 'behaviorTypes', 'behaviorSubtypes'));
    }
}
