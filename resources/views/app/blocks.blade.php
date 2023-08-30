@component('layouts.app')
    <x-metadata page="blocks" />

    @section('content')
        <x-page-headers.blocks
            :forged-count="$forgedCount"
            :missed-count="$missedCount"
            :total-rewards="$totalRewards"
            :largest-amount="$largestAmount"
        />

        <x-ark-container>
            <livewire:block-table />
        </x-ark-container>
    @endsection
@endcomponent
