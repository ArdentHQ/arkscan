@component('layouts.app')
    <x-metadata page="exchanges" />

    @section('content')
        <x-ark-container container-class="flex flex-col">
            <x-information-pages.header
                :title="trans('pages.exchanges.title')"
                :subtitle="trans('pages.exchanges.subtitle')"
            >
                <livewire:exchange-table-filter />
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
