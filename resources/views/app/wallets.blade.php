@component('layouts.app')
    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    <x-metadata page="wallets" />

    @section('content')
        <x-ark-container>
            <livewire:wallet-table />
        </x-ark-container>
    @endsection
@endcomponent
