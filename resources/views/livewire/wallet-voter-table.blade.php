<div id="block-list" class="w-full">
    @if($wallets->isNotEmpty())
        <div class="relative flex items-center justify-between">
            <h4>@lang('pages.voters_by_wallet.subtitle')</h4>
        </div>

        <x-skeletons.wallets>
            <x-tables.desktop.wallets :wallets="$wallets" without-truncate use-vote-weight />

            <x-tables.mobile.wallets :wallets="$wallets" />

            <x-general.pagination :results="$wallets" class="mt-8" />

            <script>
                window.addEventListener('livewire:load', () => window.livewire.on('pageChanged', () => scrollToQuery('#block-list')));
            </script>
        </x-skeletons.wallets>
    @else
        <div class="flex flex-col justify-center pt-8 space-y-8">
            <img src="/images/search/empty.svg" class="h-32" />

            <span class="text-center">@lang('pages.voters_by_wallet.no_results', [$username])</span>
        </div>
    @endif
</div>
