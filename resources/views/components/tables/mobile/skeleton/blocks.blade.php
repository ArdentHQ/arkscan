@php
    $items = ['text', 'text', 'text', 'number', 'number', 'number'];

    if (Network::canBeExchanged()) {
        $items[] = 'number';
    }
@endphp

<x-table-skeleton
    device="mobile"
    :items="$items"
/>
