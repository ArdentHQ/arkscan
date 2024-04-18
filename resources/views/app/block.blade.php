@component('layouts.app')
    <x-metadata page="block" :detail="['blockid' => $block->id()]" />

    @section('content')
        <x-page-headers.block :block="$block" />

        <div>
            <x-block.page.details :block="$block" />

            <x-block.page.generated-by :block="$block" />

            <x-block.page.summary :block="$block" />

            <x-general.page-section.confirmations :model="$block" />

            @if ($block->transactionCount() > 0)
                <x-block.page.transaction-list :block="$block" />
            @endif
        </div>
    @endsection
@endcomponent
