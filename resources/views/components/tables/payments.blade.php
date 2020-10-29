<div id="payments-list" class="w-full">
    <div wire:loading>
        <x-payments.table-desktop-skeleton />

        <x-payments.table-mobile-skeleton />
    </div>

    <div wire:loading.remove>
        <x-payments.table-desktop :payments="$payments" />

        <x-payments.table-mobile :payments="$payments" />
    </div>
</div>
