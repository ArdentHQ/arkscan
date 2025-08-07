@props([
    'wallet',
    'voters',
])

<div
    x-show="tab === 'voters'"
    id="voters-list"
    {{ $attributes->class('w-full') }}
>
    <div class="w-full">
        <x-tables.toolbars.toolbar :result-count="$voters->total()" />

        <x-skeletons.wallet.voters
            :row-count="$this->perPage"
            :paginator="$voters"
        >
            <x-tables.desktop.wallet.voters
                :wallets="$voters"
                :no-results-message="$this->votersNoResultsMessage"
                without-truncate
            />

            <x-tables.mobile.wallet.voters
                :wallets="$voters"
                :no-results-message="$this->votersNoResultsMessage"
            />
        </x-skeletons.wallet.voters>

        <x-general.pagination.table :results="$voters" />
    </div>

    <x-webhooks.reload-voters :public-key="$wallet->publicKey()" />
</div>
