<x-details.address
    :title="trans('general.transaction.sender')"
    :transaction="$transaction"
    :model="$transaction->sender()"
    icon="app-volume" />

<x-details.address
    :title="trans('general.transaction.multi_signature_address')"
    :transaction="$transaction"
    :model="$transaction->multiSignatureWallet()"
    icon="app-volume" />

<x-details.generic :title="trans('general.transaction.block_id')" icon="app-block-id">
    <span class="flex items-center">
        <a href="{{ route('block', $transaction->blockId()) }}" class="font-semibold link">
            <x-truncate-middle :value="$transaction->blockId()" :length="32" />
        </a>
        <x-ark-clipboard :value="$transaction->blockId()" class="flex items-center w-auto h-auto text-theme-secondary-600" no-styling />
    </span>
</x-details.generic>

<x-details.generic :title="trans('general.transaction.timestamp')" icon="app-timestamp">
    {{ $transaction->timestamp() }}
</x-details.generic>

<x-details.generic :title="trans('general.transaction.nonce')" icon="app-nonce" without-border>
    <x-number>{{ $transaction->nonce() }}</x-number>
</x-details.generic>
