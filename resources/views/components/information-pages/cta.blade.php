@props(['text'])

<div class="flex flex-col justify-between items-center py-6 px-6 mt-6 w-full text-center rounded-xl sm:flex-row sm:py-2 bg-theme-primary-100 sm:text-start dark:bg-theme-secondary-800">
    <span class="font-semibold sm:text-lg dark:text-white text-theme-primary-900">
        {!! $text !!}
    </span>

    {{ $slot }}
</div>
