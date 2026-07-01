@php
    $imageSrc = $model->image_path
        ? asset('storage/' . $model->image_path)
        : asset('images/tas.svg');
    $inputId = 'crop-file-' . $model->id;
@endphp

<div class="overflow-hidden rounded-xl border border-zinc-200 dark:border-zinc-700">
    @once
    <script>
    window.imageCropper = function () {
        var nw = 0, nh = 0;
        return {
            modalOpen: false,
            objectUrl: null,
            zoom: 1,
            uploading: false,
            progress: 0,
            error: null,

            openFile: function (event) {
                var file = event.target.files[0];
                if (!file) return;
                event.target.value = '';
                if (this.objectUrl) URL.revokeObjectURL(this.objectUrl);
                this.objectUrl = URL.createObjectURL(file);
                var self = this;
                var img = new Image();
                img.onload = function () {
                    nw = img.naturalWidth;
                    nh = img.naturalHeight;
                    self.zoom = 1;
                    self.modalOpen = true;
                };
                img.src = this.objectUrl;
            },

            confirmCrop: function () {
                var size = Math.min(nw, nh) / this.zoom;
                var x = (nw - size) / 2;
                var y = (nh - size) / 2;
                var src = this.objectUrl;
                var wire = this.$wire;
                var self = this;
                var img = new Image();
                img.onload = function () {
                    var canvas = document.createElement('canvas');
                    canvas.width = 800;
                    canvas.height = 800;
                    canvas.getContext('2d').drawImage(img, x, y, size, size, 0, 0, 800, 800);
                    canvas.toBlob(function (blob) {
                        var file = new File([blob], 'image.jpg', { type: 'image/jpeg' });
                        self.cancel();
                        self.uploading = true;
                        self.progress = 0;
                        self.error = null;
                        wire.upload(
                            'imageUpload',
                            file,
                            function () { self.uploading = false; },
                            function () { self.uploading = false; self.error = 'Upload failed. Please try again.'; },
                            function (e) { self.progress = e.detail.progress; }
                        );
                    }, 'image/jpeg', 0.88);
                };
                img.src = src;
            },

            cancel: function () {
                this.modalOpen = false;
                this.zoom = 1;
                if (this.objectUrl) {
                    URL.revokeObjectURL(this.objectUrl);
                    this.objectUrl = null;
                }
            }
        };
    };
    </script>
    @endonce
    {{-- Image display: Livewire morphs this so it updates after upload/delete --}}
    <div class="relative">
        <img src="{{ $imageSrc }}" alt="Portrait" class="aspect-square w-full object-cover">
        @if ($model->image_path)
            <button type="button"
                wire:click="deleteImage"
                wire:confirm="Remove this image?"
                class="absolute top-2 right-2 rounded-full bg-black/50 p-1 text-white hover:bg-black/70">
                <flux:icon name="x-mark" class="size-4" />
            </button>
        @endif
    </div>

    {{-- Upload label: Livewire morphs this so text updates --}}
    <div class="px-4 py-3">
        <label for="{{ $inputId }}" class="flex cursor-pointer items-center gap-2 text-sm text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200">
            <flux:icon name="arrow-up-tray" class="size-4 shrink-0" />
            <span>{{ $model->image_path ? 'Replace image' : 'Upload image' }}</span>
        </label>
        @error('imageUpload')
            <div class="mt-1 text-xs text-red-500">{{ $message }}</div>
        @enderror
    </div>

    {{-- Alpine crop section: wire:ignore preserves Alpine state across Livewire re-renders --}}
    <div wire:ignore x-data="imageCropper()">
        <input id="{{ $inputId }}" type="file" accept="image/*" x-on:change="openFile" class="sr-only" />

        <div x-show="uploading" x-cloak class="px-4 pb-3 text-xs text-zinc-400">
            Uploading <span x-text="progress + '%'"></span>
        </div>
        <div x-show="error" x-cloak x-text="error" class="px-4 pb-3 text-xs text-red-500"></div>

        {{-- Crop modal --}}
        <div
            x-show="modalOpen"
            x-cloak
            x-on:keydown.escape.window="cancel"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4"
        >
            <div class="w-full max-w-sm rounded-xl bg-white shadow-xl dark:bg-zinc-900">
                <div class="border-b border-zinc-200 px-6 py-4 dark:border-zinc-700">
                    <h3 class="text-base font-semibold text-zinc-900 dark:text-white">Crop Image</h3>
                </div>
                <div class="space-y-4 p-6">
                    <div class="mx-auto overflow-hidden rounded-lg bg-black" style="width: 280px; height: 280px;">
                        <img
                            :src="objectUrl"
                            :style="`width: 100%; height: 100%; object-fit: cover; transform: scale(${zoom}); transform-origin: center;`"
                            alt=""
                        >
                    </div>
                    <div>
                        <label class="mb-1 block text-xs text-zinc-500 dark:text-zinc-400">Zoom</label>
                        <input type="range" x-model="zoom" min="1" max="3" step="0.01" class="w-full">
                    </div>
                </div>
                <div class="flex justify-end gap-2 border-t border-zinc-200 px-6 py-4 dark:border-zinc-700">
                    <flux:button type="button" variant="ghost" x-on:click="cancel">Cancel</flux:button>
                    <flux:button type="button" variant="primary" x-on:click="confirmCrop">Crop & Upload</flux:button>
                </div>
            </div>
        </div>
    </div>
</div>
