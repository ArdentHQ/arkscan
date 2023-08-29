@props([
    'model',
    'withUsername' => false,
])

<div class="flex justify-center items-center space-x-2">
    <x-general.identity
        :model="$model"
        :without-username="! $withUsername"
        without-reverse
        without-icon
    />
</div>
