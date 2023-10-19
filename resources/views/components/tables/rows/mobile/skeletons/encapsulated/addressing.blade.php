@props([
    'generic' => false,
])

<div {{ $attributes->class('space-y-2') }}>
    <x-loading.text />

    <div class="space-y-2 sm:space-y-1">
        <div class="flex space-x-2">
            <x-loading.text width="w-[39px]" height="h-[21px]" />
            <x-loading.text height="h-[21px]" />
        </div>

        @if ($generic)
            <div class="flex space-x-2">
                <x-loading.text width="w-[39px]" height="h-[21px]" />
                <x-loading.text height="h-[21px]" />
            </div>
        @endif
    </div>
</div>
