<div id="payments-list" class="w-full">
    <div class="w-full" wire:loading>
        <x-payments.table-desktop-skeleton />

        <x-payments.table-mobile-skeleton />
    </div>

    <div class="w-full" wire:loading.remove>
        <x-payments.table-desktop :payments="$payments" />

        <x-payments.table-mobile :payments="$payments" />
    </div>
</div>
