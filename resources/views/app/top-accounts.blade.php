@component('layouts.app')
    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    <x-metadata page="top-accounts" />

    @section('content')
        <x-page-headers.generic
            :title="trans('pages.wallets.title')"
            :subtitle="trans('pages.wallets.subtitle')"
        />

        <div class="px-6 pb-8 md:px-10 md:pb-6 md:mx-auto md:max-w-7xl">
            <livewire:top-accounts-table />
        </div>
    @endsection
@endcomponent
