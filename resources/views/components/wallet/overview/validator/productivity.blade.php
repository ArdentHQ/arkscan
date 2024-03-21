@props(['wallet'])

@php
    $productivity = $wallet->isActive() ? $wallet->productivity() : 0;
    $isLow = $wallet->isActive() && $productivity < config('arkscan.productivity.danger');
    $isMedium = ! $isLow && $wallet->isActive() && $productivity >= config('arkscan.productivity.danger') && $productivity < config('arkscan.productivity.warning');
    $isHigh = ! $isMedium && $wallet->isActive() && $productivity >= config('arkscan.productivity.warning');
@endphp

<x-wallet.overview.item-entry :title="trans('pages.wallet.validator.productivity_title')">
    <x-slot name="value">
        @unless($wallet->isResigned())
            <div @class([
                'flex items-center space-x-2',
                'text-theme-secondary-500 dark:text-theme-dark-700' => ! $wallet->isActive(),
                'text-theme-danger-700 dark:text-theme-danger-400' => $isLow,
                'text-theme-warning-700' => $isMedium,
                'text-theme-success-700 dark:text-theme-success-500' => $isHigh,
            ])>
                <div>
                    <x-percentage>
                        {{ $productivity }}
                    </x-percentage>
                </div>

                <div @class([
                    'w-4 h-4 rounded-full border',
                    'border-theme-secondary-200 bg-theme-secondary-400 dark:border-theme-dark-800 dark:bg-theme-dark-700' => ! $wallet->isActive(),
                    'border-theme-danger-200 bg-theme-danger-700 dark:border-theme-danger-700 dark:bg-theme-danger-400' => $isLow,
                    'border-theme-warning-200 bg-theme-warning-700 dark:border-theme-warning-800 dark:bg-theme-warning-700' => $isMedium,
                    'border-theme-success-200 bg-theme-success-700 dark:border-theme-success-600 dark:bg-theme-success-500' => $isHigh,
                ])></div>
            </div>
        @endunless
    </x-slot>
</x-wallet.overview.item-entry>
