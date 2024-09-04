@component('layouts.app')
    <x-metadata page="validator-monitor" />

    @section('content')
        <x-page-headers.generic
            :title="trans('pages.validator-monitor.title')"
            :subtitle="trans('pages.validator-monitor.subtitle')"
        />

        <livewire:validators.monitor />
    @endsection
@endcomponent
