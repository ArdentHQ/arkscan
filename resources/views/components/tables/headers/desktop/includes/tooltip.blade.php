@props([
    'text',
    'type' => 'info',
])

<div {{ $attributes->class('h-5 w-5 ark-info-element') }}>
    <x-ark-info
        :tooltip="$text"
        :type="$type"
    />
</div>
