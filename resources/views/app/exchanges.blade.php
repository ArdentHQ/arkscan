@component('layouts.app')
    <x-metadata page="exchanges" />

    @section('content')
        <x-ark-container container-class="flex flex-col">
            <x-information-pages.header
                :title="trans('pages.exchanges.title')"
                :subtitle="trans('pages.exchanges.subtitle')"
            >
                <div class="flex flex-col space-y-2 w-full sm:flex-row sm:space-y-0 sm:space-x-3 md-lg:w-auto">
                    <x-exchanges.type-dropdown />

                    <x-exchanges.pair-dropdown />
                </div>
            </x-information-pages.header>

            <div class="mt-6">
                <livewire:exchange-table />
            </div>

            <x-information-pages.cta
                :text="trans('pages.exchanges.get_listed')"
                breakpoint="md"
                padding="mt-8 md:mt-6"
            >
                <livewire:modals.submit-exchange />
            </x-information-pages.cta>
        </x-ark-container>
    @endsection
@endcomponent
