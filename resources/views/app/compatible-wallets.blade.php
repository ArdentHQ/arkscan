@component('layouts.app')
    <x-metadata page="compatible-wallets" />

    @section('content')
        <x-ark-container>
            <x-information-pages.header
                :title="trans('pages.compatible-wallets.title')"
                :subtitle="trans('pages.compatible-wallets.subtitle')"
            />

            <x-compatible-wallets.section-divider />

            <x-compatible-wallets.arkvault />

            <x-compatible-wallets.section-divider />

            <x-compatible-wallets.wallet-grid />

            <x-information-pages.cta :text="trans('pages.compatible-wallets.get_listed')">
                <livewire:modals.submit-wallet />
            </x-information-pages.cta>
        </x-ark-container>
    @endsection
@endcomponent
