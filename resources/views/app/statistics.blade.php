@component('layouts.app')
    <x-metadata page="statistics" />

    @section('content')
        <x-page-headers.generic
            :title="trans('pages.statistics.title')"
            :subtitle="trans('pages.statistics.subtitle')"
        />

        <livewire:stats-highlights />

        <div class="px-6 pt-6 pb-8 border-t-4 md:px-10 md:mx-auto md:max-w-7xl md:border-0 border-theme-secondary-200 dark:border-theme-dark-950">
            <x-stats.insights-wrapper>
                <livewire:stats.insight-all-time-transactions />
                <livewire:stats.insight-current-average-fee />
                <livewire:stats.insight-all-time-fees-collected />
            </x-stats.insights-wrapper>

            <livewire:stats.chart />
        </div>
    @endsection
@endcomponent
