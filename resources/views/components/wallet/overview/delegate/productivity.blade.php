@props(['wallet'])

@php ($productivity = $wallet->isActive() ? $wallet->productivity() : 0)

<x-wallet.overview.item-entry
    :title="trans('pages.wallet.delegate.productivity_title')"
    :has-empty-value="! $wallet->isDelegate()"
>
    <x-slot name="value">
        <div @class([
            'flex items-center space-x-2',
            'text-theme-secondary-500 dark:text-theme-secondary-700' => $productivity <= 0,
            'text-theme-danger-700 dark:text-theme-danger-400' => $productivity < 50,
            'text-theme-warning-700' => $productivity > 50 && $productivity <= 90,
            'text-theme-success-700' => $productivity > 90,
        ])>
            <div>
                <x-percentage>
                    {{ $productivity }}
                </x-percentage>
            </div>

            <div @class([
                'w-4 h-4 rounded-full border',
                'border-theme-secondary-200 bg-theme-secondary-400 dark:border-theme-secondary-800 dark:bg-theme-secondary-700' => ! $wallet->isActive() || ($wallet->isActive() && $productivity <= 0),
                'border-theme-danger-200 bg-theme-danger-700 dark:border-theme-danger-700 dark:bg-theme-danger-400' => $wallet->isActive() && $productivity < 50,
                'border-theme-warning-200 bg-theme-warning-700 dark:border-theme-warning-800 dark:bg-theme-warning-700' => $wallet->isActive() && $productivity > 50 && $productivity <= 90,
                'border-theme-success-200 bg-theme-success-700 dark:border-theme-success-800 dark:bg-theme-success-700' => $wallet->isActive() && $productivity > 90,
            ])></div>
        </div>
    </x-slot>
</x-wallet.overview.item-entry>
