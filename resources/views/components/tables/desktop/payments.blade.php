<div class="hidden w-full table-container md:block">
    <table>
        <thead>
            <tr>
                <x-tables.headers.desktop.address name="general.transaction.recipient" />
                <x-tables.headers.desktop.number name="general.transaction.amount" />
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <x-ark-tables.row>
                    <x-ark-tables.cell
                        wire:key="{{ $payment->address() }}-address"
                        last-on="md"
                    >
                        <x-general.identity :model="$payment" without-truncate />
                    </x-ark-tables.cell>
                    <x-ark-tables.cell
                        responsive
                        breakpoint="md"
                        class="text-right"
                    >
                        <x-tables.rows.desktop.amount :model="$payment" />
                    </x-ark-tables.cell>
                </x-ark-tables.row>
            @endforeach
        </tbody>
    </table>
</div>
