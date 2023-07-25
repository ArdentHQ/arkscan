@component('layouts.app')
    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    <x-metadata page="block" :detail="['blockid' => $block->id()]" />

    @section('content')
        <x-page-headers.block :block="$block" />

        <div>
            <x-block.page.details :block="$block" />

            <x-block.page.generated-by :block="$block" />

            <x-block.page.summary :block="$block" />

            <x-general.page-section.confirmations :model="$block" />

            <x-block.page.transaction-list :block="$block" />
        </div>
    @endsection
@endcomponent
