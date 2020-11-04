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
                <tr>
                    <td wire:key="{{ $payment->address() }}-address">
                        <x-general.identity :model="$payment" without-truncate />
                    </td>
                    <td class="hidden text-right md:table-cell">
                        <x-tables.rows.desktop.amount :model="$payment" />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
