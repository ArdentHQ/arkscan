@props([
    'identifier',
    'size' => 'w-6 h-6 md:w-8 md:h-8',
])

<div @class([
    'overflow-hidden rounded-full',
    $size,
])>
    <div class="object-cover w-full h-full">
        {!! Avatar::make($identifier) !!}
    </div>
</div>
