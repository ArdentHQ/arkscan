@component('layouts.app')
    {{-- <x-metadata page="transaction" :detail="['txid' => $transaction->id()]" /> --}}

    @section('content')
        <x-page-headers.migration />

        {{-- <x-details.grid>
            <x-dynamic-component :component="$transaction->typeComponent()" :transaction="$transaction" />
        </x-details.grid>

        @if($transaction->hasExtraData())
            <x-dynamic-component :component="$transaction->extensionComponent()" :transaction="$transaction" />
        @endif --}}
    @endsection
@endcomponent
