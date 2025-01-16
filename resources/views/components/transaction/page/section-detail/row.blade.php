@props([
    'transaction',
    'title',
    'value' => null,
    'valueClass' => null,
    'tooltip' => null,
    'allowEmpty' => false,
])

<x-general.page-section.row
    :header-width="$transaction->recipient()->isContract() ? 'sm:w-[151px]' : 'sm:w-[132px]'"
    :title="$title"
    :value="$value"
    :value-class="$valueClass"
    :tooltip="$tooltip"
    :allow-empty="$allowEmpty"
>
    {{ $slot }}
</x-general.page-section.row>
