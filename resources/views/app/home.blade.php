@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @section('content')
        <x-general.search.header />

        <x-home.charts :prices="$prices" :fees="$fees" :aggregates="$aggregates" />

        <x-home.content />
    @endsection

@endcomponent
