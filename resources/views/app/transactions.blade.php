@component('layouts.app')
    <x-metadata page="transactions" />

    @section('content')
        <x-page-headers.transactions
            :transaction-count="$transactionCount"
            :volume="$volume"
            :total-fees="$totalFees"
            :average-fee="$averageFee"
        />

        <x-ark-container>
            <livewire:transaction-table />
        </x-ark-container>
    @endsection
@endcomponent
