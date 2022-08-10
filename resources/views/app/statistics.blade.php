@component('layouts.app')
    <x-metadata page="statistics" />

    @section('content')
        <livewire:stats-highlights/>

        <x-ark-container>
            <x-stats.insights-wrapper>
                <livewire:stats.insight-all-time-transactions/>
                <livewire:stats.insight-current-average-fee/>
                <livewire:stats.insight-all-time-fees-collected/>
            </x-stats.insights-wrapper>

            <livewire:stats.chart/>
        </x-ark-container>
    @endsection
@endcomponent
