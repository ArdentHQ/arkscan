<div id="block-list" class="w-full">
    @if($blocks->isNotEmpty())
        <div class="flex relative justify-between items-center">
            <h3>@lang('pages.blocks_by_wallet.table_title')</h3>
        </div>

        <x-skeletons.blocks without-generator>
            <x-tables.desktop.blocks :blocks="$blocks" without-generator />

            <x-tables.mobile.blocks :blocks="$blocks" without-generator />

            <x-general.pagination :results="$blocks" class="mt-8" />

            <x-script.onload-scroll-to-query selector="#block-list" />
        </x-skeletons.blocks>
    @else
        <x-general.no-results :text="trans('pages.blocks_by_wallet.no_results', [$username])" />
    @endif
</div>
