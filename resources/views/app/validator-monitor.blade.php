@component('layouts.app')
    <x-metadata page="validator-monitor" />

    @section('content')
        <x-page-headers.generic
            :title="trans('pages.validator-monitor.title')"
            :subtitle="trans('pages.validator-monitor.subtitle')"
        />

        <div class="px-6 pb-6 md:px-10 md:mx-auto md:max-w-7xl">
            <livewire:validator-data-boxes />
        </div>

        <x-general.mobile-divider />

        <div class="px-6 pt-6 pb-8 md:px-10 md:pt-0 md:mx-auto md:max-w-7xl">
            <livewire:validators.monitor />
        </div>
    @endsection
@endcomponent
