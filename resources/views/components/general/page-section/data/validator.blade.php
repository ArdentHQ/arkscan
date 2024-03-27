@props(['validator'])

<span>
    <a
        href="{{ route('wallet', $validator->address()) }}"
        class="link"
    >
        @if ($validator->hasUsername())
            {{ $validator->username() }}
        @else
            <x-truncate-middle>{{ $validator->address() }}</x-truncate-middle>
        @endif
    </a>
</span>
