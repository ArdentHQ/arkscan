<div id="wallet-list" class="w-full">
    <x-skeletons.wallets>
        <div class="mb-6">
            <h1 class="text-2xl font-semibold xl:mb-1.5 text-theme-secondary-900">
                @lang('pages.wallets.title')
            </h1>
            <span class="text-xs font-semibold text-theme-secondary-500">
                @lang('pages.wallets.subtitle')
            </span>
        </div>

        <x-tables.desktop.wallets :wallets="$wallets" />

        <x-tables.mobile.wallets :wallets="$wallets" />

        <x-general.pagination :results="$wallets" class="mt-8" />

        <x-script.onload-scroll-to-query selector="#wallet-list" />
    </x-skeletons.wallets>
</div>
