@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @section('content')
        <x-general.search.header />

        <div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
            <div class="py-16 content-container md:px-8">
                <div x-data="{
                    dropdownOpen: false,
                    transactionTypeFilter: '{{ $transactionTypeFilter }}',
                    transactionTypeFilterLabel: '@lang('forms.search.transaction_types.' . $transactionTypeFilter)',
                }" x-cloak class="w-full">
                    <div class="w-full mb-8">
                        <div class="relative flex flex-col justify-between md:items-end md:flex-row md:justify-start">
                            <h2 class="mb-8 md:mb-0">@lang('pages.transactions.title')</h2>

                            <div>
                                <x-transaction-table-filter :type="$transactionTypeFilter"/>
                            </div>
                        </div>
                    </div>

                    <livewire:transaction-table />
                </div>
            </div>
        </div>
    @endsection

@endcomponent
