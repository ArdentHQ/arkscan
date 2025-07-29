@props([
    'model',
    'withoutAge' => false,
])

@php ($hasFailedStatus = $model->hasFailedStatus())

<div class="flex flex-col md:space-y-1 xl:space-y-0">
    <div @class([
        'leading-4.25',
        'flex space-x-2 box-border h-[21px] items-center bg-theme-danger-50 dark:bg-transparent border border-transparent dark:border-theme-failed-state-bg px-1.5 rounded' => $hasFailedStatus,
    ])>
        <a
            href="{{ $model->url() }}"
            @class([
                'mx-auto text-sm font-semibold whitespace-nowrap link leading-4.25',
                '!text-theme-danger-700 dark:!text-theme-failed-state-text' => $hasFailedStatus,
            ])
        >
            <x-truncate-middle>{{ $model->hash() }}</x-truncate-middle>
        </a>

        @if ($hasFailedStatus)
            <div>
                <x-ark-icon
                    name="circle.minus-small"
                    size="xs"
                    class="text-theme-danger-700 dark:text-theme-failed-state-text"
                />
            </div>
        @endif
    </div>

    @unless ($withoutAge)
        <x-tables.rows.desktop.encapsulated.age
            :model="$model"
            class="hidden text-xs md:block xl:hidden leading-3.75"
        />
    @endunless
</div>
