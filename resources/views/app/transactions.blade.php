@component('layouts.app')
    <x-metadata page="transactions" />

    @section('content')
        <x-page-headers.generic
            :title="trans('pages.transactions.title')"
            :subtitle="trans('pages.transactions.subtitle')"
        />

        <x-ark-container>
            <livewire:transaction-table />
        </x-ark-container>
    @endsection
@endcomponent
