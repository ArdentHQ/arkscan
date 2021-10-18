<div id="block-list" class="w-full">
    <x-skeletons.blocks>
        <x-tables.desktop.blocks :blocks="$blocks" />

        <x-tables.mobile.blocks :blocks="$blocks" />

        <x-general.pagination :results="$blocks" class="mt-8" />

        <x-script.onload-scroll-to-query selector="#block-list" />
    </x-skeletons.blocks>
</div>
