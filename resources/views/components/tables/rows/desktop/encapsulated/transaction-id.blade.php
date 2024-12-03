@props([
    'model',
    'withoutAge' => false,
])

@php ($hasFailedStatus = $model->hasFailedStatus())

<div class="flex flex-col md:space-y-1 xl:space-y-0">
    <div @class([
        'flex space-x-2 items-center bg-theme-danger-50 dark:bg-transparent border border-transparent dark:border-theme-failed-state-bg py-0.5 px-1.5 rounded' => $hasFailedStatus,
    ])>
        <a
            href="{{ $model->url() }}"
            @class([
                'mx-auto text-sm font-semibold whitespace-nowrap link leading-4.25',
                '!text-theme-danger-700 dark:!text-theme-failed-state-text' => $hasFailedStatus,
            ])
        >
            <x-truncate-middle>{{ $model->id() }}</x-truncate-middle>
        </a>

        @if ($hasFailedStatus)
            <x-ark-icon
                name="cross-small"
                size="w-3 h-3"
                class="text-theme-danger-700 dark:text-theme-failed-state-text"
            />
        @endif
    </div>

    @unless ($withoutAge)
        <x-tables.rows.desktop.encapsulated.age
            :model="$model"
            class="hidden text-xs md:block xl:hidden leading-3.75"
        />
    @endunless
</div>
