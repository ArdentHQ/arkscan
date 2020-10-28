<div class="space-y-8 divide-y md:hidden">
    @foreach ($payments as $payment)
        <div class="flex flex-col space-y-3 w-full pt-8 {{ $loop->first ? '' : 'border-t'}} border-theme-secondary-300">
            <x-tables.rows.mobile.recipient :model="$payment" />

            <x-tables.rows.mobile.amount :model="$payment" />
        </div>
    @endforeach
</div>
