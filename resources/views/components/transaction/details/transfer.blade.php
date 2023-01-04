<x-grid.sender :model="$transaction" />

<x-grid.recipient :model="$transaction" />

<x-grid.block-id :model="$transaction" />

<x-grid.timestamp :model="$transaction" />

@if ($transaction->migratedAddress() !== null && $transaction->isMigration())
    <x-grid.migrated-address :model="$transaction" />
@else
    <x-grid.vendor-field :model="$transaction" />
@endif

<x-grid.nonce :model="$transaction" />
