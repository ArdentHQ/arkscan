@props([
    'model',
    'withoutAge' => false,
])

@php ($hasFailedStatus = $model->hasFailedStatus())

<div class="flex flex-col md:space-y-1 xl:space-y-0">
    <div @class(['flex space-x-2 items-center bg-theme-danger-50 py-0.5 px-1.5 rounded' => $hasFailedStatus])>
        <a
            href="{{ $model->url() }}"
            @class([
                'mx-auto text-sm font-semibold whitespace-nowrap link leading-4.25',
                '!text-theme-danger-700' => $hasFailedStatus,
            ])
        >
            <x-truncate-middle>{{ $model->id() }}</x-truncate-middle>
        </a>

        @if ($hasFailedStatus)
            <x-ark-icon
                name="cross"
                size="w-2.5 h-2.5"
                class="text-theme-danger-700"
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
