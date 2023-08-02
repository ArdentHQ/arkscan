@props(['transaction'])

<x-page-headers.id
    :model="$transaction"
    :title="trans('pages.transaction.transaction_id')"
    :copy-tooltip="trans('pages.transaction.transaction_id_copied')"
/>
