<x-tables.mobile.includes.encapsulated>
    <x-skeleton :row-count="$rowCount">
        <x-tables.rows.mobile>
            <x-slot name="header">
                <div class="flex items-center space-x-3 h-5">
                    <x-tables.rows.mobile.skeletons.encapsulated.number />

                    <div>
                        <x-tables.rows.mobile.skeletons.text class="sm:hidden" />
                        <x-tables.rows.mobile.skeletons.encapsulated.address class="hidden sm:block" />
                    </div>
                </div>
            </x-slot>

            <x-tables.rows.mobile.skeletons.encapsulated.text />

            <x-tables.rows.mobile.skeletons.encapsulated.text />

            {{-- A third even though wallets don't always have a name --}}
            <x-tables.rows.mobile.skeletons.encapsulated.text />
        </x-tables.rows.mobile>
    </x-skeleton>
</x-tables.mobile.includes.encapsulated>
