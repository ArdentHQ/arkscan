@component('layouts.app')
    <x-metadata page="delegate-monitor" />

    @section('content')
        <x-page-headers.generic
            :title="trans('pages.delegate-monitor.title')"
            :subtitle="trans('pages.delegate-monitor.subtitle')"
        />

        <livewire:delegates.monitor />
    @endsection
@endcomponent
