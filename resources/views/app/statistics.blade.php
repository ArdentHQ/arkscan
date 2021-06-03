@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @section('content')
        <livewire:stats-highlights />

        <x-ark-container class="bg-white dark:bg-theme-secondary-900">
            {{--@TODO: <livewire:stats-insights />--}}
            {{--@TODO: <livewire:stats-chart />--}}
        </x-ark-container>
    @endsection

@endcomponent
