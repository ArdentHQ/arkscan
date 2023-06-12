@props(['model'])

<div class="flex flex-col md:space-y-1 xl:space-y-0">
    <a
        href="{{ $model->url() }}"
        class="mx-auto link text-sm font-semibold whitespace-nowrap leading-[17px]"
    >
        <x-truncate-middle>{{ $model->id() }}</x-truncate-middle>
    </a>

    <x-tables.rows.desktop.encapsulated.age
        :model="$model"
        class="text-xs hidden md:block xl:hidden leading-[15px]"
    />
</div>
