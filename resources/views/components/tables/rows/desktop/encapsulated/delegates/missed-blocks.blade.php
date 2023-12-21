@props(['model'])

@php
    $state = 'success';
    $missedBlocks = $model->missedBlocks();
    if ($model->isActive()) {
        $missedPercentage = $model->productivity();

        if ($missedPercentage < config('arkscan.productivity.danger')) {
            $state = 'danger';
        } elseif ($missedPercentage < config('arkscan.productivity.warning')) {
            $state = 'warning';
        }
    } else {
        $state = 'inactive';
    }
@endphp

<x-general.badge
    class="text-center min-w-[30px]"
    :colors="Arr::toCssClasses([
        'bg-theme-success-100 border-theme-success-100 text-theme-success-700 dark:border-theme-success-700 dark:text-theme-success-500' => $state === 'success',
        'bg-theme-orange-light border-theme-orange-light text-theme-orange-dark dark:border-theme-orange-600 dark:text-theme-warning-400' => $state === 'warning',
        'bg-theme-danger-100 border-theme-danger-100 text-theme-danger-700 dark:border-[#AA6868] dark:text-[#F39B9B] dim:border-[#AB8282] dim:text-[#CAA0A0]' => $state === 'danger',
        'border-transparent bg-theme-secondary-200 dark:border-theme-dark-800 dark:text-theme-dark-500 encapsulated-badge' => $state === 'inactive'
    ])"
>
    {{ $missedBlocks }}
</x-general.badge>
