@props(['missedBlocks'])

<div
    x-show="tab === 'missed-blocks'"
    id="missed-blocks-list"
    {{ $attributes->class('w-full') }}
>
    <div class="w-full">
        <x-tables.toolbars.validators.missed-blocks :blocks="$missedBlocks" />

        <x-skeletons.validators.missed-blocks
            :row-count="$this->perPage"
            :paginator="$missedBlocks"
        >
            <x-tables.desktop.validators.missed-blocks
                :blocks="$missedBlocks"
                :no-results-message="$this->missedBlocksNoResultsMessage"
            />

            <x-tables.mobile.validators.missed-blocks
                :blocks="$missedBlocks"
                :no-results-message="$this->missedBlocksNoResultsMessage"
            />
        </x-skeletons.validators.missed-blocks>

        <x-general.pagination.table :results="$missedBlocks" />
    </div>
</div>
