@props([
    'model',
    'withoutUsername' => true,
])

<div class="flex justify-center items-center space-x-2">
    <x-general.identity
        :model="$model"
        :without-username="$withoutUsername"
    />
</div>
