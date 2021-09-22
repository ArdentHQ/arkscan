<div id="payments-list" class="w-full">
    <x-loading.visible>
        <x-tables.desktop.skeleton.payments />

        <x-tables.mobile.skeleton.payments />
    </x-loading.visible>

    <x-loading.hidden>
        <x-tables.desktop.payments :payments="$payments" />

        <x-tables.mobile.payments :payments="$payments" />
    </x-loading.hidden>
</div>
