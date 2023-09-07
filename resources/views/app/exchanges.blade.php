@component('layouts.app')
    <x-metadata page="exchanges" />

    @section('content')
        <x-page-headers.generic
            :title="trans('pages.exchanges.title')"
            :subtitle="trans('pages.exchanges.subtitle')"
        >
            <livewire:exchange-table-filter />
        </x-page-headers.generic>

        <div class="flex flex-col px-6 pt-6 pb-8 border-t-4 md:px-10 md:pt-0 md:pb-6 md:mx-auto md:max-w-7xl md:border-0 border-theme-secondary-200 dark:border-theme-dark-950">
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
        </div>
    @endsection
@endcomponent
