<x-table-skeleton
    device="mobile"
    :items="($withoutGenerator ?? false) ?
        ['text', 'text', 'number', 'number', 'number', 'number'] :
        ['text', 'text', 'address', 'number', 'number', 'number', 'number']"
/>
