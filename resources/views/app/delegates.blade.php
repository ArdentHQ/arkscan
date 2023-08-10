@component('layouts.app')
    <x-metadata page="delegates" />

    @section('content')
        <livewire:delegates.header-stats />

        <div class="px-6 pt-6 pb-8 border-t-4 sm:border-0 md:px-10 md:pt-0 md:pb-6 md:mx-auto md:max-w-7xl border-theme-secondary-200 dark:border-theme-dark-950">
            <livewire:delegates.tabs />
        </div>
    @endsection
@endcomponent
