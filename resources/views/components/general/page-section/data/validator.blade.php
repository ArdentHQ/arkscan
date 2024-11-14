@props(['validator'])

<span>
    <a
        href="{{ route('wallet', $validator->address()) }}"
        class="link"
    >
        <div class="hidden md:inline">
            {{ $validator->address() }}
        </div>

        <div class="md:hidden">
            <x-truncate-middle>
                {{ $validator->address() }}
            </x-truncate-middle>
        </div>
    </a>
</span>
