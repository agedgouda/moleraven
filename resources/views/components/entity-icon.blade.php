@props(['model'])
@if($model?->image_path)
    <img src="{{ asset('storage/' . $model->image_path) }}" alt="" {{ $attributes->merge(['class' => 'h-6 w-6 shrink-0 rounded-full object-cover']) }}>
@endif
