@component('layouts.app')
    <x-metadata page="delegates" />

    @section('content')
        <livewire:delegates.header-stats />

        <div class="px-6 pb-8 pt-6 border-t-4 sm:border-0 border-theme-secondary-200 dark:border-theme-dark-950 md:px-10 md:pt-0 md:pb-6 md:mx-auto md:max-w-7xl">
            <livewire:delegates.tabs />
        </div>
    @endsection
@endcomponent
