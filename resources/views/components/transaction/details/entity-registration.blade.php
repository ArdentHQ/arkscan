<x-details.address
    :title="trans('general.transaction.sender')"
    :transaction="$transaction"
    :address="$transaction->sender()"
    icon="app-volume" />

<x-details.generic :title="trans('general.transaction.fee')" icon="app-volume">
    {{ $transaction->fee() }}
</x-details.generic>

<x-details.generic :title="trans('general.transaction.block_id')" icon="app-volume">
    <a href="{{ route('block', $transaction->blockId()) }}" class="font-semibold link">
        <x-truncate-middle :value="$transaction->blockId()" />
    </a>
</x-details.generic>

<x-details.generic :title="trans('general.transaction.timestamp')" icon="app-volume">
    {{ $transaction->timestamp() }}
</x-details.generic>

<x-details.generic :title="trans('general.transaction.nonce')" icon="app-volume">
    {{ $transaction->nonce() }}
</x-details.generic>

<x-details.generic :title="trans('general.transaction.confirmations')" icon="app-volume">
    {{ $transaction->confirmations() }}
</x-details.generic>
