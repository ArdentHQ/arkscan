@component('layouts.app')
    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    <x-metadata page="migration" />

    @section('content')
        <div class="pb-8 dark:bg-black bg-theme-secondary-100">
            <x-page-headers.migration />

            <livewire:migration.stats />
        </div>

        <x-ark-container>
            <livewire:migration.transactions />
        </x-ark-container>
    @endsection
@endcomponent
