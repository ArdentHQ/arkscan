<div id="exchange-list" class="w-full">
    <x-skeletons.exchanges>
        <x-tables.desktop.exchanges :exchanges="$exchanges" />

        <x-tables.mobile.exchanges :exchanges="$exchanges" />
    </x-skeletons.exchanges>
</div>
