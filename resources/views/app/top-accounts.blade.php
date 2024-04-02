@component('layouts.app')
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

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                hideTableTooltipsOnLivewireEvent(/^top-accounts-table$/);
            });
        </script>
    @endpush
@endcomponent
