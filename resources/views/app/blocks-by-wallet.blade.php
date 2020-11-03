@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    @section('content')
        <x-page-headers.wallet.blocks :wallet="$wallet" />

        <div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
            <div class="py-16 content-container md:px-8">
                <div x-cloak class="w-full">
                    <livewire:wallet-block-table :public-key="$wallet->publicKey()" :username="$wallet->username()" />
                </div>
            </div>
        </div>
    @endsection

@endcomponent
