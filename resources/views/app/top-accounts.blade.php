@component('layouts.app')
    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    <x-metadata page="top-accounts" />

    @section('content')
        <x-ark-container>
            <livewire:top-accounts-table />
        </x-ark-container>
    @endsection
@endcomponent
