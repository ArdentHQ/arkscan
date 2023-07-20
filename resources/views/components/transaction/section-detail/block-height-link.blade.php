@props([
    'id',
    'height',
])

<span>
    <a
        href="{{ route('block', $id) }}"
        class="link"
    >
        <x-number>{{ $height }}</x-number>
    </a>
</span>
