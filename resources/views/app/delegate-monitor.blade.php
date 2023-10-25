@component('layouts.app')
    <x-metadata page="delegate-monitor" />

    @section('content')
        <x-page-headers.generic
            :title="trans('pages.delegate-monitor.title')"
            :subtitle="trans('pages.delegate-monitor.subtitle')"
        />

        <x-general.header class="overflow-auto">
            <div class="px-8 md:px-10 md:w-full">
                <livewire:delegate-data-boxes />
            </div>
        </x-general.header>

        <x-ark-container>
            <livewire:delegates.monitor />
        </x-ark-container>
    @endsection
@endcomponent
