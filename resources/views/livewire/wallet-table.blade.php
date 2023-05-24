<div id="wallet-list" class="w-full">
    <x-skeletons.wallets>
        <div class="flex flex-col mb-6">
            <h1 class="text-lg font-semibold sm:text-2xl xl:mb-1.5 text-theme-secondary-900">
                @lang('pages.wallets.title')
            </h1>
            <span class="text-xs font-semibold text-theme-secondary-500">
                @lang('pages.wallets.subtitle')
            </span>
        </div>

        <x-tables.desktop.wallets :wallets="$wallets" />

        <x-tables.mobile.wallets :wallets="$wallets" />

        <x-general.pagination.table :results="$wallets" />

        <x-script.onload-scroll-to-query selector="#wallet-list" />
    </x-skeletons.wallets>
</div>
