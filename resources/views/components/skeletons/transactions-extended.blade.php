<x-loading.visible>
    <x-tables.desktop.skeleton.transactions use-confirmations use-direction />

    <x-tables.mobile.skeleton.transactions use-confirmations use-direction />
</x-loading.visible>

<x-loading.hidden>
    {{ $slot }}
</x-loading.hidden>
