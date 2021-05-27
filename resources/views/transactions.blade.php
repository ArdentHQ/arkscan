@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @section('content')
        <x-ark-container>
            <div x-data="{
                dropdownOpen: false,
                transactionTypeFilter: '{{ $transactionTypeFilter }}',
                transactionTypeFilterLabel: '@lang('forms.search.transaction_types.' . $transactionTypeFilter)',
            }" x-cloak class="w-full">
                <div class="mb-8 w-full">
                    <div class="flex relative flex-col justify-between md:items-end md:flex-row md:justify-start">
                        <h2 class="mb-8 md:mb-0">@lang('pages.transactions.title')</h2>

                        <div>
                            <x-transaction-table-filter :type="$transactionTypeFilter"/>
                        </div>
                    </div>
                </div>

                <livewire:transaction-table />
            </div>
        </x-ark-container>
    @endsection

@endcomponent
