@props(['validator'])

<span>
    <a
        href="{{ route('wallet', $validator->address()) }}"
        class="link"
    >
        {{ $validator->username() }}
    </a>
</span>
