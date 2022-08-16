@component('layouts.app')
    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    <x-metadata page="block" :detail="['blockid' => $block->id()]" />

    @section('content')
        <x-page-headers.block :block="$block" />

        <x-details.grid>
            <x-grid.height :model="$block" />

            <x-grid.timestamp :model="$block" />

            <x-grid.fee :model="$block" />

            <x-grid.confirmations :model="$block" />
        </x-details.grid>

        @if($hasTransactions)
            <x-ark-container class="border-t border-theme-secondary-300 dark:border-theme-secondary-800">
                <div id="transaction-list" class="w-full">
                    <div class="flex relative justify-between items-end mb-3">
                        <h3>@lang('pages.block.transactions')</h3>
                    </div>

                    <livewire:block-transactions-table :block-id="$block->id()" />
                </div>
            </x-ark-container>
        @endif
    @endsection
@endcomponent
