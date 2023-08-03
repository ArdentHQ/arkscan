@component('layouts.app')
    <x-metadata page="delegates" />

    @section('content')
        <x-general.header class="overflow-auto">
            <div class="px-8 md:px-10 md:w-full">
                <livewire:delegate-data-boxes />
            </div>
        </x-general.header>

        <x-ark-container>
            <livewire:delegates.tabs />
        </x-ark-container>
    @endsection
@endcomponent
