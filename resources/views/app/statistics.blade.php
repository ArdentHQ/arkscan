@component('layouts.app')
    <x-metadata page="statistics" />

    @section('content')
        <x-page-headers.generic
            :title="trans('pages.statistics.title')"
            :subtitle="trans('pages.statistics.subtitle')"
        />

        <div class="pb-6 md:px-10 md:mx-auto md:max-w-7xl">
            <livewire:stats.gas-tracker />
        </div>

        <livewire:stats.highlights />

        <div>
            <div class="py-6 px-6 space-y-6 border-t-4 md:px-10 md:mx-auto md:max-w-7xl md:border-0 border-theme-secondary-200 dark:border-theme-dark-950">
                <x-stats.information-cards />
            </div>
        </div>

        <x-stats.insights />
    @endsection
@endcomponent
