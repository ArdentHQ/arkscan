@component('layouts.app')
    <x-metadata page="exchanges" />

    @section('content')
        <div class="hidden sm:flex flex-col px-6 pt-8 md:px-10 md:mx-auto md:max-w-7xl">
            <div class="text-lg md:text-2xl font-semibold text-theme-secondary-900 dark:text-theme-dark-50">
                @lang('pages.exchanges.live_price_chart')
            </div>

            <livewire:exchanges.chart />
        </div>

        <x-general.mobile-divider class="hidden sm:block my-6" />
        <x-general.desktop-divider class="my-8" />

        <x-page-headers.generic
            :title="trans('pages.exchanges.title')"
            :subtitle="trans('pages.exchanges.subtitle')"
            class="sm:space-y-4 md:justify-between md-lg:flex-row md-lg:items-center md-lg:space-y-0 sm:pt-0"
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
