@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @section('content')
        <div class="dark:bg-theme-secondary-900">
            <div class="flex-col pt-16 mb-16 space-y-6 content-container">
                <x-general.search.header-slim :title="trans('pages.monitor.title')" />

                <livewire:monitor-statistics />
            </div>
        </div>

        {{-- @TODO: MSQ Banner --}}

        <div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
            <div class="py-16 content-container md:px-8">
                <div x-data="{
                    dropdownOpen: false,
                    component: 'list',
                    status: 'active',
                }" x-cloak class="w-full">
                    <livewire:monitor-delegate-tabs />

                    <div x-show="component === 'monitor'">
                        <livewire:monitor-network />
                    </div>

                    <div x-show="component === 'list'">
                        <livewire:monitor-delegate-table />
                    </div>
                </div>
            </div>
        </div>
    @endsection

@endcomponent
