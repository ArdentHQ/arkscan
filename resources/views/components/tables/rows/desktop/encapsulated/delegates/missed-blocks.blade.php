@props(['model'])

@php
    $missedBlocks = $model->missedBlocks();

    $state = 'success';
    if ($missedBlocks > 0) {
        $forgedBlocks = $model->forgedBlocks();
        $missedPercentage = (($forgedBlocks - $missedBlocks) / $forgedBlocks) * 100;

        if ($missedPercentage < config('arkscan.productivity.danger')) {
            $state = 'danger';
        } elseif ($missedPercentage < config('arkscan.productivity.warning')) {
            $state = 'warning';
        }
    }
@endphp

{{-- TODO: use <x-general.badge when PR #465 is merged --}}
<div {{ $attributes->class([
    'text-xs font-semibold rounded border px-[3px] py-[2px] leading-[15px] dark:bg-transparent min-w-[30px] text-center',
    'bg-theme-success-100 border-theme-success-100 text-theme-success-700 dark:border-theme-success-700 dark:text-theme-success-500' => $state === 'success',
    'bg-theme-orange-light border-theme-orange-light text-theme-orange-dark dark:border-theme-orange-dark dark:text-theme-warning-400' => $state === 'warning',
    'bg-theme-danger-100 border-theme-danger-100 text-theme-danger-700 dark:border-[#AA6868] dark:text-[#F39B9B]' => $state === 'danger',
]) }}>
    {{ $missedBlocks }}
</div>