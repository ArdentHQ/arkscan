@props([
    'delegate',
])

<span>
    <a
        href="{{ route('wallet', $delegate->address())}}"
        class="link"
    >
        {{ $delegate->username() }}
    </a>
</span>
