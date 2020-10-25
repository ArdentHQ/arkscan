@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @push('metatags')
        <meta property="og:title" content="@lang('metatags.transaction.title')" />
        <meta property="og:description" content="@lang('metatags.transaction.description')">
    @endpush

    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    @section('breadcrumbs')
        <x-general.breadcrumbs :crumbs="[
            ['route' => 'home', 'label' => trans('menus.home')],
            ['label' => trans('menus.transaction')],
        ]" />
    @endsection

    @section('content')
        <x-transaction.header :transaction="$transaction" />

        <x-details.grid>
            @if($transaction->isTransfer())
                <x-transaction.details.transfer :transaction="$transaction" />
            @endif

            @if($transaction->isMultiPayment())
                <x-transaction.details.multi-payment :transaction="$transaction" />
            @endif

            @if($transaction->isMultiSignature())
                <x-transaction.details.multi-signature :transaction="$transaction" />
            @endif

            @if($transaction->isEntityRegistration())
                <x-transaction.details.entity-registration :transaction="$transaction" />
            @endif

            @if($transaction->isSelfReceiving())
                <x-transaction.details.self-receiving :transaction="$transaction" />
            @endif
        </x-details.grid>
    @endsection

@endcomponent
