@php
    $imageSrc = $model->image_path
        ? asset('storage/' . $model->image_path)
        : asset('images/tas.svg');
@endphp

<div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
    <div class="relative">
        <img src="{{ $imageSrc }}" alt="Portrait" class="h-48 w-full object-cover">
        @if ($model->image_path)
            <button type="button"
                wire:click="deleteImage"
                wire:confirm="Remove this image?"
                class="absolute top-2 right-2 rounded-full bg-black/50 p-1 text-white hover:bg-black/70">
                <flux:icon name="x-mark" class="size-4" />
            </button>
        @endif
    </div>
    <div class="px-4 py-3">
        <label class="flex cursor-pointer items-center gap-2 text-sm text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200">
            <flux:icon name="arrow-up-tray" class="size-4 shrink-0" />
            <span>{{ $model->image_path ? 'Replace image' : 'Upload image' }}</span>
            <input type="file" accept="image/*" wire:model="imageUpload" class="sr-only" />
        </label>
        <div wire:loading wire:target="imageUpload" class="mt-1 text-xs text-zinc-400">Uploading...</div>
        @error('imageUpload')
            <div class="mt-1 text-xs text-red-500">{{ $message }}</div>
        @enderror
    </div>
</div>
