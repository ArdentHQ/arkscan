@props([
    'title',
    'icon',
    'class' => null,
    'slot'  => null
])

<div {{ $attributes->merge(['class' => 'flex justify-between pb-4 space-x-4'])->only('class') }}>
    <div class="flex flex-col flex-1 space-y-2 min-w-0 min-h-0">
        <span class="text-sm font-semibold text-theme-secondary-500 dark:text-theme-secondary-700">
            {{ $title }}
        </span>

        @empty($slot)
            <span class="font-semibold break-words text-theme-secondary-500 dark:text-theme-secondary-200">
                @lang('generic.not_specified')
            </span>
        @else
            <span class="font-semibold break-words text-theme-secondary-900 dark:text-theme-secondary-200">
                {{ $slot }}
            </span>
        @endif
    </div>

    <div class="flex justify-center mt-1 w-12 h-12">
        <div class="circled-icon text-theme-secondary-900 border-theme-secondary-900 dark:text-theme-secondary-600 dark:border-theme-secondary-600">
            <x-ark-icon
                :name="$icon"
                class="text-theme-secondary-900 dark:text-theme-secondary-600"
            />
        </div>
    </div>
</div>
