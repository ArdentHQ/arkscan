<x-details.address
    :title="trans('general.transaction.sender')"
    :transaction="$transaction"
    :model="$transaction->sender()"
    icon="app-volume" />

<x-details.generic :title="trans('general.transaction.recipient')" icon="app-volume">
    {{ $transaction->recipientsCount() }} @lang('general.transaction.recipients')
</x-details.generic>

<x-details.generic :title="trans('general.transaction.block_id')" icon="app-block-id">
    <a href="{{ route('block', $transaction->blockId()) }}" class="font-semibold link">
        <x-truncate-middle :value="$transaction->blockId()" :length="32" />
    </a>
</x-details.generic>

<x-details.generic :title="trans('general.transaction.timestamp')" icon="app-timestamp">
    {{ $transaction->timestamp() }}
</x-details.generic>

<x-details.generic :title="trans('general.transaction.smartbridge')" icon="app-smartbridge" without-border>
    {{ $transaction->vendorField() }}
</x-details.generic>

<x-details.generic :title="trans('general.transaction.nonce')" icon="app-nonce" without-border>
    {{ $transaction->nonce() }}
</x-details.generic>
