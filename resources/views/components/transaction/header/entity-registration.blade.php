<x-general.entity-header-item
    :title="trans('pages.transaction.transaction_type')"
    icon="app-transactions.{{ $transaction->iconType() }}"
>
    <x-slot name="text">
        @lang('pages.transaction.'.$transaction->entityType())
    </x-slot>
</x-general.entity-header-item>

<x-general.entity-header-item
    :title="trans('pages.transaction.name')"
    icon="app-transactions-amount"
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
        <a href="https://cloudflare-ipfs.com/ipfs/{{ $transaction->entityHash() }}" class="font-semibold link">
            <x-truncate-middle :value="$transaction->entityHash()" />
        </a>
    </x-slot>
</x-general.entity-header-item>
