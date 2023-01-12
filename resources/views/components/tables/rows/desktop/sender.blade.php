@props([
    'model',
    'dynamicTruncate' => false,
])

<x-general.identity
    :model="$model->sender()"
    :dynamic-truncate="$dynamicTruncate"
/>
