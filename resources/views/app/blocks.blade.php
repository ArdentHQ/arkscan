@component('layouts.app')
    <x-metadata page="blocks" />

    @section('content')
        <x-page-headers.blocks
            :forged-count="$forgedCount"
            :missed-count="$missedCount"
            :total-rewards="$totalRewards"
            :max-transactions="$maxTransactions"
        />

        <div class="px-6 pt-6 pb-8 border-t-4 md:px-10 md:pt-0 md:pb-6 md:mx-auto md:max-w-7xl md:border-0 border-theme-secondary-200 dark:border-theme-dark-950">
            <livewire:block-table />
        </div>
    @endsection

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                hideTableTooltipsOnLivewireEvent(/^block-table$/);
            });
        </script>
    @endpush
@endcomponent
