@component('layouts.app')
    <x-metadata page="transactions" />

    @section('content')
        <x-page-headers.transactions
            :transaction-count="$transactionCount"
            :volume="$volume"
            :total-fees="$totalFees"
            :average-fee="$averageFee"
        />

        <div class="px-6 pt-6 pb-8 border-t-4 md:px-10 md:pt-0 md:pb-6 md:mx-auto md:max-w-7xl md:border-0 border-theme-secondary-200 dark:border-theme-dark-950">
            <livewire:transaction-table />
        </div>
    @endsection
@endcomponent
