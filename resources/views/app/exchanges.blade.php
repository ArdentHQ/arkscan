@component('layouts.app')
    <x-metadata page="exchanges" />

    @section('content')
        <x-page-headers.generic
            :title="trans('pages.exchanges.title')"
            :subtitle="trans('pages.exchanges.subtitle')"
            class="md:justify-between sm:space-y-4 md-lg:flex-row md-lg:items-center md-lg:space-y-0"
        >
            <livewire:exchange-table-filter />
        </x-page-headers.generic>

        <div class="flex flex-col px-6 pb-8 md:px-10 md:pb-6 md:mx-auto md:max-w-7xl">
            <livewire:exchange-table />

            <x-information-pages.cta
                :text="trans('pages.exchanges.get_listed')"
                breakpoint="md"
                padding="mt-8 md:mt-6"
            >
                <livewire:modals.submit-exchange />
            </x-information-pages.cta>
        </div>
    @endsection
@endcomponent
