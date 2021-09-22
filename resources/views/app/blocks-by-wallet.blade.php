@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    @section('content')
        <x-page-headers.wallet.blocks :wallet="$wallet" />

        <div class="bg-white border-t-2 dark:border-black border-theme-secondary-200 dark:bg-theme-secondary-900">
            <x-ark-container>
                <div x-cloak class="w-full">
                    <livewire:wallet-block-table :public-key="$wallet->publicKey()" :username="$wallet->username()" />
                </div>
            </x-ark-container>
        </div>
    @endsection

@endcomponent
