@component('layouts.app')
    <x-metadata page="statistics" />

    @section('content')
        <x-page-headers.generic
            :title="trans('pages.statistics.title')"
            :subtitle="trans('pages.statistics.subtitle')"
        />

        <livewire:stats.highlights />

        <div class="py-6 px-6 border-t-4 space-y-6 md:px-10 md:mx-auto md:max-w-7xl md:border-0 border-theme-secondary-200 dark:border-theme-dark-950">
            <livewire:stats.gas-tracker />

            <x-stats.information-cards />
        </div>

        <x-stats.insights />
    @endsection
@endcomponent
