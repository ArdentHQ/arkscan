@props(['model'])

<a
    class="block p-3 md:-mx-3 space-y-2 min-w-0 cursor-pointer group transition-default rounded-[10px] dark:hover:bg-black hover:bg-theme-secondary-100"
    href="{{ $model->url() }}"
>
    {{ $slot }}
</a>
