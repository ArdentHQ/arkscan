@component('layouts.app')
    <x-metadata page="migration" />

    @section('content')
        <div class="bg-theme-secondary-100 dark:bg-black pb-8">
            <x-page-headers.migration />

            <livewire:migration.stats />
        </div>

        <x-ark-container>
            <livewire:migration.transactions />
        </x-ark-container>
    @endsection
@endcomponent
