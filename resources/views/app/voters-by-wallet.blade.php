@component('layouts.app')
    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    <x-metadata page="wallet-voters" :detail="['delegate' => $wallet->username()]" />

    @section('content')
        <x-page-headers.wallet.voters :wallet="$wallet" />

        <x-ark-container class="border-t-2 dark:border-black border-theme-secondary-200">
            <div class="w-full" x-cloak>
                <livewire:wallet-voter-table :public-key="$wallet->publicKey()" :username="$wallet->username()" />
            </div>
        </x-ark-container>
    @endsection
@endcomponent
