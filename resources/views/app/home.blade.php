@component('layouts.app')
    <x-metadata page="home" />

    @section('content')
        <x-home.header />

        <x-general.mobile-divider class="mt-6" />

        <div class="px-6 pb-8 mt-6 md:px-10 md:pb-6 md:mx-auto md:max-w-7xl">
            <livewire:home.tables />

            <x-home.footer />
        </div>
    @endsection
@endcomponent
