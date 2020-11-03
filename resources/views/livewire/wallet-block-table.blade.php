<div id="block-list" class="w-full">
    @if($blocks->isNotEmpty())
        <div class="relative flex items-center justify-between">
            <h2 class="text-xl sm:text-2xl">@lang('pages.blocks_by_wallet.title')</h2>
        </div>

        <x-skeletons.blocks without-generator>
            <x-tables.desktop.blocks :blocks="$blocks" without-generator />

            <x-tables.mobile.blocks :blocks="$blocks" without-generator />

            <x-general.pagination :results="$blocks" class="mt-8" />

            <script>
                window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#block-list')));
            </script>
        </x-skeletons.blocks>
    @else
        <div class="flex flex-col justify-center pt-8 space-y-8">
            <img src="/images/search/empty.svg" class="h-32" />

            <span class="text-center">@lang('pages.blocks_by_wallet.no_results', [$username])</span>
        </div>
    @endif
</div>
