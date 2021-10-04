
@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])
    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    <x-metadata page="wallet-voters" :detail="['delegate' => $wallet->username()]" />

    @section('content')
        <x-page-headers.wallet.voters :wallet="$wallet" />

        <div class="bg-white border-t-2 dark:border-black border-theme-secondary-200 dark:bg-theme-secondary-900">
            <x-ark-container>
                <div x-cloak class="w-full">
                    <livewire:wallet-voter-table :public-key="$wallet->publicKey()" :username="$wallet->username()" />
                </div>
            </x-ark-container>
        </div>
    @endsection
@endcomponent
