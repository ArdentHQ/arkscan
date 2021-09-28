@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])
    <x-metadata page="delegates" />

    @section('content')
        <x-general.header class="overflow-auto">
            <div class="px-8 md:px-10 md:w-full">
                <livewire:delegate-data-boxes />
            </div>
        </x-general.header>

        <div class="bg-white dark:bg-theme-secondary-900">
            <x-ark-container>
                <div
                    x-data="function() {
                        const initialSelected = window.location.search.split('tab=')[1] || 'active';
                        const initialComponent = initialSelected === 'monitor' ? 'monitor' : 'table';

                        const extraData = {
                            dropdownOpen: false,
                            component: initialComponent,
                        };

                        function onSelected (selected) {
                            {{-- Push the tab to the search query parameters --}}
                            const { protocol, host, pathname } = window.location;
                            const newurl = `${protocol}//${host}${pathname}?tab=${selected}`;
                            window.history.pushState({ path: newurl },'',newurl);

                            if (selected === 'monitor') {
                                this.component = 'monitor';
                            } else {
                                this.component = 'table';
                                Livewire.emit('filterByDelegateStatus', selected);
                            }
                        }

                        return Tabs(
                            initialSelected,
                            extraData,
                            onSelected
                        );
                    }()"
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
