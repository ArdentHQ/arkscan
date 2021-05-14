@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    @section('breadcrumbs')
        <x-ark-breadcrumbs :crumbs="[
            ['route' => 'home', 'label' => trans('menus.home')],
            ['label' => trans('menus.block')],
        ]" />
    @endsection

    @section('content')
        <x-page-headers.block :block="$block" />

        <x-details.grid>
            <x-grid.height :model="$block" />

            <x-grid.timestamp :model="$block" />

            <x-grid.reward :model="$block" />

            <x-grid.fee :model="$block" />

            <x-grid.confirmations :model="$block" />
        </x-details.grid>

        @if($hasTransactions)
            <div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
                <div class="py-16 content-container">
                    <div id="transaction-list" class="w-full">
                        <div class="flex relative justify-between items-end mb-8">
                            <h4>@lang('pages.block.transactions')</h4>
                        </div>

                        <livewire:block-transactions-table :block-id="$block->id()" />
                    </div>
                </div>
            </div>
        @endif
    @endsection

@endcomponent
