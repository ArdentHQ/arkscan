@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    @section('breadcrumbs')
        <x-ark-breadcrumbs :crumbs="[
            ['route' => 'home', 'label' => trans('menus.home')],
            ['label' => trans('menus.transaction')],
        ]" />
    @endsection

    @section('content')
        <x-page-headers.transaction :transaction="$transaction" />

        <x-details.grid>
            <x-dynamic-component :component="$transaction->typeComponent()" :transaction="$transaction" />
        </x-details.grid>

        @if($transaction->hasExtraData())
            <x-dynamic-component :component="$transaction->extensionComponent()" :transaction="$transaction" />
        @endif
    @endsection

@endcomponent
