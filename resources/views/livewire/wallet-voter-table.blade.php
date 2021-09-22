<div id="block-list" class="w-full">
    @if($wallets->isNotEmpty())
        <div class="flex relative justify-between items-center">
            <h3>@lang('pages.voters_by_wallet.subtitle')</h3>
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
        <x-general.no-results :text="trans('pages.voters_by_wallet.no_results', [$username])" />
    @endif
</div>
