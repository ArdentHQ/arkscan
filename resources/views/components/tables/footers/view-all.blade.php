@props([
    'results',
    'countSuffix',
    'route',
])

<x-tables.footers.footer>
    <div class="font-semibold sm:mr-8 dark:text-theme-dark-200">
        <span>
            <x-number>{{ $results->total() }}</x-number>
        </span>

        <span>{{ $countSuffix }}</span>
    </div>

    <div class="flex w-full sm:w-auto">
        <a
            href="{{ $route }}"
            class="py-1.5 w-full h-8 button-secondary"
        >
            <div class="flex justify-center items-center space-x-2">
                <span>@lang('pagination.view_all')</span>

                <x-ark-icon
                    name="arrows.chevron-right-small"
                    size="xs"
                />
            </div>
        </a>
    </div>
</x-tables.footers.footer>
