@component('layouts.app')
    <x-metadata page="compatible-wallets" />

    @section('content')
        <x-page-headers.generic
            :title="trans('pages.compatible-wallets.title')"
            :subtitle="trans('pages.compatible-wallets.subtitle')"
        />

        <div class="px-6 pt-6 pb-8 border-t-4 md:px-10 md:pt-0 md:pb-6 md:mx-auto md:max-w-7xl md:border-0 border-theme-secondary-200 dark:border-theme-dark-950">
            <x-compatible-wallets.arkvault />

            <x-compatible-wallets.section-divider class="dim:!bg-theme-dark-950 dim:!text-theme-dark-950" />

            <x-compatible-wallets.wallet-grid />

            <x-information-pages.cta :text="trans('pages.compatible-wallets.get_listed')">
                <livewire:modals.submit-wallet />
            </x-information-pages.cta>
        </div>
    @endsection
@endcomponent
