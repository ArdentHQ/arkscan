<div class="hidden w-full table-container md:block">
    <table>
        <thead>
            <tr>
                <th><span class="pl-14">@lang('general.transaction.recipient')</span></th>
                <th width="180" class="hidden text-right lg:table-cell">@lang('general.transaction.amount')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>
                        <x-general.identity :model="$payment" without-truncate />
                    </td>
                    <td class="hidden text-right lg:table-cell">
                        <x-tables.rows.desktop.amount :model="$payment" />
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
