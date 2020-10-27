<x-details.address
    :title="trans('general.transaction.sender')"
    :transaction="$transaction"
    :address="$transaction->sender()"
    icon="app-volume" />

<x-details.address
    :title="trans('general.transaction.recipient')"
    :transaction="$transaction"
    :address="$transaction->recipient()"
    icon="app-volume" />

<x-details.generic :title="trans('general.transaction.block_id')" icon="app-block-id">
    <a href="{{ route('block', $transaction->blockId()) }}" class="font-semibold link">
        <x-truncate-middle :value="$transaction->blockId()" />
    </a>
</x-details.generic>

<x-details.generic :title="trans('general.transaction.timestamp')" icon="app-timestamp">
    {{ $transaction->timestamp() }}
</x-details.generic>

<x-details.generic :title="trans('general.transaction.smartbridge')" icon="app-smartbridge">
    {{ $transaction->vendorField() }}
</x-details.generic>

<x-details.generic :title="trans('general.transaction.nonce')" icon="app-nonce">
    {{ $transaction->nonce() }}
</x-details.generic>
