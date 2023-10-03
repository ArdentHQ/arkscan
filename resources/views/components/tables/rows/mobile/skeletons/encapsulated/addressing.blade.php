@props([
    'generic' => false,
])

<div class="space-y-2">
    <x-loading.text />

    <div class="flex space-x-2">
        <x-loading.text width="w-[40px]" />
        <x-loading.text />
    </div>

    @if ($generic)
        <div class="flex space-x-2">
            <x-loading.text width="w-[40px]" />
            <x-loading.text />
        </div>
    @endif
</div>
