@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @section('content')
        <div class="dark:bg-theme-secondary-900">
            <x-ark-container container-class="flex flex-col space-y-5">
                <x-general.search.header-slim :title="trans('pages.delegates.title')" />

                <livewire:delegate-statistics />
            </x-ark-container>
        </div>

        <div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
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
