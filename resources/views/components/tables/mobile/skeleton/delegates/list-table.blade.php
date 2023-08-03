@php
    $items = ['text', 'number', 'number', 'number'];
    if ($this->showMissedBlocks) {
        $items[] = 'text';
    }
@endphp

<x-table-skeleton
    device="mobile"
    :items="$items"
/>
