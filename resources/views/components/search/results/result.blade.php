@props(['model'])

<a
    class="block p-3 -mx-3 min-w-0 cursor-pointer group/result rounded-[10px] transition-default dark:hover:bg-black hover:bg-theme-secondary-200"
    href="{{ $model->url() }}"
    x-on:blur="blurHandler"
>
    {{ $slot }}
</a>
