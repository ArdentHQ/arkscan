@props([
    'icon',
    'label',
    'disabled' => false,
])

<div class="flex items-center py-4 px-7 space-x-4 bg-white rounded-lg">
    <span class="flex items-center justify-center w-10 h-10 border-2 rounded-full  @if($disabled) border-theme-secondary-500 text-theme-secondary-500 @else border-theme-secondary-900 text-theme-secondary-900 @endif">
        <x-ark-icon :name="$icon" />
    </span>
    <span class="flex flex-col justify-between">
        <span class="text-sm font-semibold text-theme-secondary-500">{{$label}}:</span>
        <span class="text-lg font-semibold @if($disabled) text-theme-secondary-500 @else text-theme-secondary-900 @endif">
            {{ $slot }}
        </span>
    </span>

    @isset($side)
        {{ $side }}
    @endisset
</div>
