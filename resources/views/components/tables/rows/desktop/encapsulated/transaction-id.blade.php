@props([
    'model',
    'withoutAge' => false,
])

<div class="flex flex-col md:space-y-1 xl:space-y-0">
    <a
        href="{{ $model->url() }}"
        class="mx-auto text-sm font-semibold whitespace-nowrap link leading-4.25"
    >
        <x-truncate-middle>{{ $model->id() }}</x-truncate-middle>
    </a>

    @unless ($withoutAge)
        <x-tables.rows.desktop.encapsulated.age
            :model="$model"
            class="hidden text-xs md:block xl:hidden leading-3.75"
        />
    @endunless
</div>
