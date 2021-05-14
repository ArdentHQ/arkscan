@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @section('breadcrumbs')
        <x-ark-breadcrumbs :crumbs="[
            ['route' => 'home', 'label' => trans('menus.home')],
            ['label' => trans('menus.delegate_monitor')],
        ]" />
    @endsection

    @section('content')
        <div class="dark:bg-theme-secondary-900">
            <div class="flex-col pt-16 mb-16 space-y-5 content-container">
                <x-general.search.header-slim :title="trans('pages.delegates.title')" />

                <livewire:delegate-statistics />
            </div>
        </div>

        <div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
            <div class="py-16 content-container">
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
            </div>
        </div>
    @endsection

@endcomponent
