<div id="wallet-list" class="w-full">
    <div class="flex flex-col mb-6">
        <h1 class="text-lg font-semibold sm:text-2xl xl:mb-1.5 text-theme-secondary-900">
            @lang('pages.wallets.title')
        </h1>

        <span class="text-xs font-semibold text-theme-secondary-500">
            @lang('pages.wallets.subtitle')
        </span>
    </div>

    <x-skeletons.top-accounts :row-count="$this->perPage">
        <x-tables.desktop.top-accounts :wallets="$wallets" />

        <x-tables.mobile.top-accounts :wallets="$wallets" />
    </x-skeletons.top-accounts>

    <x-general.pagination.table :results="$wallets" />

    <x-script.onload-scroll-to-query selector="#wallet-list" />
</div>
