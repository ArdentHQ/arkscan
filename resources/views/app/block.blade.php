@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    @section('content')
        <x-page-headers.block :block="$block" />

        <x-details.grid>
            <x-grid.height :model="$block" />

            <x-grid.timestamp :model="$block" />

            <x-grid.fee :model="$block" />

            <x-grid.confirmations :model="$block" />
        </x-details.grid>

        @if($hasTransactions)
            <div class="bg-white border-t border-theme-secondary-300 dark:border-theme-secondary-800 dark:bg-theme-secondary-900">
                <x-ark-container>
                    <div id="transaction-list" class="w-full">
                        <div class="flex relative justify-between items-end mb-3">
                            <h2>@lang('pages.block.transactions')</h2>
                        </div>

                        <livewire:block-transactions-table :block-id="$block->id()" />
                    </div>
                </x-ark-container>
            </div>
        @endif
    @endsection

@endcomponent
