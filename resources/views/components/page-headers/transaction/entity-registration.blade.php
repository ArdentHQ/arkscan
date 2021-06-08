<x-page-headers.transaction.icon-type :model="$transaction" as-entity />

<x-general.entity-header-item
    :title="trans('pages.transaction.name')"
    icon="app-supply"
>
    <x-slot name="text">
        {{ $transaction->entityName() }}
    </x-slot>
</x-general.entity-header-item>

<x-general.entity-header-item
    :title="trans('pages.transaction.category')"
    icon="app-smartbridge"
>
    <x-slot name="text">
        @lang('generic.not_specified')
    </x-slot>
</x-general.entity-header-item>

<x-general.entity-header-item
    :title="trans('pages.transaction.ipfs_hash')"
    icon="app-transactions.ipfs"
>
    <x-slot name="text">
        @if($transaction->entityHash())
            <x-ark-external-link url="https://cloudflare-ipfs.com/ipfs/{{ $transaction->entityHash() }}">
                <x-slot name="text">
                    <x-truncate-middle>
                        {{ $transaction->entityHash() }}
                    </x-truncate-middle>
                </x-slot>
            </x-ark-external-link>
        @else
            @lang('generic.not-available')
        @endif
    </x-slot>
</x-general.entity-header-item>
