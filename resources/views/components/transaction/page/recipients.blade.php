@props(['model'])

@php
    $recepients = $model->multiPaymentRecipients();
@endphp

<x-general.page-section.container
    :title="trans('general.recipients')"
    wrapper-class="flex flex-col flex-1 whitespace-nowrap"
    no-border
>
<x-tables.encapsulated-table>
    <thead class="dark:bg-black bg-theme-secondary-100">
        <tr class="border-b-none">
            <x-tables.headers.desktop.address name="general.address" class="w-full" />

            <x-tables.headers.desktop.number name="general.transaction.amount" class="text-right" />
        </tr>
    </thead>
    <tbody>
        @foreach($recepients as $recipient)
            <x-ark-tables.row wire:key="recipient-{{ $recipient['address'] }}-{{ $recipient['amount'] }}-{{ $loop->index }}">
                <x-ark-tables.cell>
                    <x-tables.rows.desktop.encapsulated.address truncateBreakpoint="lg" :address="$recipient['address']" />
                </x-ark-tables.cell>

                <x-ark-tables.cell class="text-right">
                    <x-general.amount-small :amount="$recipient['amount']" />
                </x-ark-tables.cell>
            </x-ark-tables.row>
        @endforeach
    </tbody>
    </x-ark-tables.table>

</x-general.page-section.container>
