@props(['model'])

<div class="flex flex-col md:space-y-1 xl:space-y-0">
    <a
        href="{{ $model->url() }}"
        class="text-sm font-semibold whitespace-nowrap link leading-4.25"
    >
        {{ number_format($model->height(), 0) }}
    </a>

    <x-tables.rows.desktop.encapsulated.age
        :model="$model"
        class="hidden text-xs md:block leading-3.75 md-lg:hidden"
    />
</div>
