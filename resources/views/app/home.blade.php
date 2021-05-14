@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @section('content')
        <x-general.search.header />

        <x-home.charts :prices="$prices" :fees="$fees" :aggregates="$aggregates" />

        <div class="bg-white dark:bg-theme-secondary-900">
            <div class="py-16 content-container">
                <livewire:latest-records />
            </div>
        </div>
    @endsection

@endcomponent
