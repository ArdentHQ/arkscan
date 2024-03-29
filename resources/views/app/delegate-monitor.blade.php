@component('layouts.app')
    <x-metadata page="delegate-monitor" />

    @section('content')
        <x-page-headers.generic
            :title="trans('pages.delegate-monitor.title')"
            :subtitle="trans('pages.delegate-monitor.subtitle')"
        />

        <div class="px-6 pb-6 md:px-10 md:mx-auto md:max-w-7xl">
            <livewire:delegate-data-boxes />
        </div>

        <x-general.mobile-divider />

        <div class="px-6 pt-6 pb-8 md:px-10 md:pt-0 md:mx-auto md:max-w-7xl">
            <livewire:delegates.monitor />
        </div>
    @endsection
@endcomponent
