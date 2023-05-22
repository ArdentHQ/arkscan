@component('layouts.app')
    <x-metadata page="exchanges" />

    @section('content')
        <x-ark-container container-class="flex flex-col space-y-6">
            <x-information-pages.header
                :title="trans('pages.exchanges.title')"
                :subtitle="trans('pages.exchanges.subtitle')"
            >
                <div class="flex flex-col space-y-2 w-full sm:flex-row sm:space-y-0 sm:space-x-3 md-lg:w-auto">
                    <x-exchanges.type-dropdown />

                    <x-exchanges.pair-dropdown />
                </div>
            </x-information-pages.header>

            <livewire:exchange-table />

            <x-information-pages.cta :text="trans('pages.exchanges.get_listed')">
                {{-- TODO: submit exchange modal --}}
                <livewire:modals.submit-wallet />
            </x-information-pages.cta>
        </x-ark-container>
    @endsection
@endcomponent
