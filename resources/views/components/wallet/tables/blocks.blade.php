@props([
    'wallet',
    'blocks',
])

<div
    x-show="tab === 'blocks'"
    id="blocks-list"
    {{ $attributes->class('w-full') }}
>
    <div class="w-full">
        <x-tables.toolbars.wallet.blocks
            :blocks="$blocks"
            :wallet="$wallet"
        />

        <x-skeletons.wallet.blocks
            :row-count="$this->perPage"
            :paginator="$blocks"
        >
            <x-tables.desktop.wallet.blocks
                :blocks="$blocks"
                :no-results-message="$this->blocksNoResultsMessage"
                without-truncate
            />

            <x-tables.mobile.wallet.blocks
                :blocks="$blocks"
                :no-results-message="$this->blocksNoResultsMessage"
            />
        </x-skeletons.wallet.blocks>

        <x-general.pagination.table :results="$blocks" />
    </div>

    <x-webhooks.reload-blocks :public-key="$wallet->publicKey()" />
</div>
