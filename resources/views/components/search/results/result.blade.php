@props(['model'])

<a
    class="block space-y-2 min-w-0 p-3 -mx-3 group cursor-pointer hover:bg-theme-secondary-100 dark:hover:bg-black transition-default rounded-[10px]"
    href="{{ $model->url() }}"
>
    {{ $slot }}
</a>
