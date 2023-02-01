@props([
    'model',
    'withoutTruncate' => false,
])

<x-general.identity
    :model="$model->sender()"
    :without-truncate="$withoutTruncate"
/>
