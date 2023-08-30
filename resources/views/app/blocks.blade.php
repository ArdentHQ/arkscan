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
            <div class="w-full">
                <div class="flex relative justify-between items-center">
                    <h1 class="mb-3">@lang('pages.blocks.title')</h1>
                </div>

                <livewire:block-table />
            </div>
        </x-ark-container>
    @endsection
@endcomponent
