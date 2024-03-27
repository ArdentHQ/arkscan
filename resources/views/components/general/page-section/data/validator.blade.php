@props(['validator'])

<span>
    <a
        href="{{ route('wallet', $validator->address()) }}"
        class="link"
    >
        @if ($validator->hasUsername())
            {{ $validator->username() }}
        @else
            <div class="hidden md:inline">
                {{ $validator->address() }}
            </div>

            <div class="md:hidden">
                <x-truncate-middle>
                    {{ $validator->address() }}
                </x-truncate-middle>
            </div>
        @endif
    </a>
</span>
