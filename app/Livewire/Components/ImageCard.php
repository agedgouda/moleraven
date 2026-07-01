<?php

namespace App\Livewire\Components;

use Flux\Flux;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class ImageCard extends Component
{
    use WithFileUploads;

    #[Locked]
    public Model $model;

    #[Locked]
    public string $folder;

    public $imageUpload;

    public function updatedImageUpload(): void
    {
        $this->validate(['imageUpload' => 'image|max:4096']);

        if ($this->model->image_path) {
            Storage::disk('public')->delete($this->model->image_path);
        }

        $path = $this->imageUpload->store($this->folder, 'public');
        $this->model->update(['image_path' => $path]);
        $this->imageUpload = null;
        Flux::toast('Image uploaded.');
    }

    public function deleteImage(): void
    {
        if ($this->model->image_path) {
            Storage::disk('public')->delete($this->model->image_path);
            $this->model->update(['image_path' => null]);
        }
    }

    public function render(): View
    {
        return view('livewire.shared.image-card');
    }
}
