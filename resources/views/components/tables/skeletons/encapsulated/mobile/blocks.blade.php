<x-tables.mobile.includes.encapsulated>
    <x-skeleton :row-count="$rowCount">
        <x-tables.rows.mobile content-class="sm:grid sm:grid-cols-5 sm:gap-6">
            <x-slot name="header">
                <x-tables.rows.mobile.skeletons.text class="sm:flex-1" />

                <x-tables.rows.mobile.skeletons.text class="sm:flex-1 sm:text-right leading-4.25" />
            </x-slot>

            <x-tables.rows.mobile.skeletons.encapsulated.text class="sm:col-span-2" />

            <x-tables.rows.mobile.skeletons.encapsulated.text class="leading-4.25" />

            <div class="sm:flex sm:flex-1 sm:col-span-2 sm:justify-end">
                <x-tables.rows.mobile.skeletons.encapsulated.text />
            </div>

            <x-tables.rows.mobile.skeletons.encapsulated.text class="sm:col-span-2 sm:w-[142px]" />

            @if (Network::canBeExchanged())
                <x-tables.rows.mobile.skeletons.encapsulated.text class="sm:col-span-2" />
            @endif
        </x-tables.rows.mobile>
    </x-skeleton>
</x-tables.mobile.includes.encapsulated>
