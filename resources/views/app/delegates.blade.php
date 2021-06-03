@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @section('content')
        <div class="dark:bg-theme-secondary-900">
            <x-ark-container container-class="flex flex-col space-y-5">
                <h1 class="header-2">
                    @lang('pages.delegates.title')
                </h1>

                <livewire:delegate-statistics />
            </x-ark-container>
        </div>

        <div class="bg-white border-t border-theme-secondary-300 dark:border-theme-secondary-800 dark:bg-theme-secondary-900">
            <x-ark-container>
                <div x-data="{
                    dropdownOpen: false,
                    component: 'table',
                    status: 'active',
                }" x-cloak class="w-full">
                    <livewire:delegate-tabs />

                    <div x-show="component === 'monitor'">
                        <livewire:delegate-monitor />
                    </div>

                    <div x-show="component === 'table'">
                        <livewire:delegate-table />
                    </div>
                </div>
            </x-ark-container>
        </div>
    @endsection

@endcomponent
