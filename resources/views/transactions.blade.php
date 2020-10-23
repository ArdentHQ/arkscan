@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @push('metatags')
        <meta property="og:title" content="@lang('metatags.transactions.title')" />
        <meta property="og:description" content="@lang('metatags.transactions.description')">
    @endpush

    @section('content')
        <x-general.search.header />

        <div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
            <div class="py-16 content-container md:px-8">
                <div x-data="{
                    dropdownOpen: false,
                    selected: 'transactions', // TODO: get rid of this, only here because of the filter
                    transactionTypeFilter: 'all',
                    transactionTypeFilterLabel: 'All',
                }" x-cloak class="w-full">
                    <x-transaction-table-filter />

                    <livewire:transaction-table />
                </div>
            </div>
        </div>
    @endsection

@endcomponent
