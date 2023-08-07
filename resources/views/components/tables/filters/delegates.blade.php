@props([
    'mobile' => false,
])

<x-general.dropdown.filter
    :mobile="$mobile"
    without-text
>
    <div class="text-center dark:text-theme-dark-200">
        @lang('general.coming_soon')
    </div>
</x-general.dropdown.filter>
