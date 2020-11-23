<span data-tippy-content="<x-currency :currency="Network::currency()">{{ $slot }}</x-currency>">
    <x-short-currency :currency="Network::currency()">
        {{ $slot }}
    </x-short-currency>
</span>
