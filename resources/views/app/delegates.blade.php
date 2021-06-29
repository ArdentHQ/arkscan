@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])
    @section('content')
        <x-general.header class="overflow-auto">
            <div class="px-8 md:px-10 md:w-full">
                <livewire:delegate-data-boxes />
            </div>
        </x-general.header>

        <div class="bg-white dark:bg-theme-secondary-900">
            <x-ark-container>
                <div
                    x-data="Tabs(
                        'active',
                        {
                            dropdownOpen: false,
                            component: 'table',
                        },
                        function(selected) {
                            if (selected === 'monitor') {
                                this.component = 'monitor';
                            } else {
                                this.component = 'table';
                                Livewire.emit('filterByDelegateStatus', selected);
                            }
                        }
                    )"
                    x-cloak
                    class="w-full"
                >
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
