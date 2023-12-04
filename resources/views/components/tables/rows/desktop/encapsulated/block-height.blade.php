@props([
    'model',
    'withoutLink' => false,
])

<div {{ $attributes->class('text-sm font-semibold flex flex-col md:space-y-1 xl:space-y-0 whitespace-nowrap leading-4.25 text-theme-secondary-900 dark:text-theme-dark-50') }}>
    @unless ($withoutLink)
        <a
            href="{{ $model->url() }}"
            class="link"
        >
            {{ number_format($model->height(), 0) }}
        </a>
    @else
        <span>{{ number_format($model->height(), 0) }}</span>
    @endunless

    <x-tables.rows.desktop.encapsulated.age
        :model="$model"
        class="hidden text-xs md:block leading-3.75 text-theme-secondary-700 md-lg:hidden dark:text-theme-dark-200"
    />
</div>
