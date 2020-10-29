<div id="payments-list" class="w-full">
    <x-loading.visible>
        <x-payments.table-desktop-skeleton />

        <x-payments.table-mobile-skeleton />
    </x-loading.visible>

    <x-loading.hidden>
        <x-payments.table-desktop :payments="$payments" />

        <x-payments.table-mobile :payments="$payments" />
    </x-loading.hidden>
</div>
