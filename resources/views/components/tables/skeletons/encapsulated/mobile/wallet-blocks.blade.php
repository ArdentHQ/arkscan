<x-tables.mobile.includes.encapsulated>
    <x-skeleton :row-count="$rowCount">
        <x-tables.rows.mobile>
            <x-slot name="header">
                <x-tables.rows.mobile.skeletons.text />

                <x-tables.rows.mobile.skeletons.text class="hidden sm:block" />

                <x-tables.rows.mobile.skeletons.text />
            </x-slot>

            <x-tables.rows.mobile.skeletons.encapsulated.text class="sm:hidden" />

            <x-tables.rows.mobile.skeletons.encapsulated.text />

            <x-tables.rows.mobile.skeletons.encapsulated.text />

            @if (Network::canBeExchanged())
                <x-tables.rows.mobile.skeletons.encapsulated.text />
            @endif
        </x-tables.rows.mobile>
    </x-skeleton>
</x-tables.mobile.includes.encapsulated>
