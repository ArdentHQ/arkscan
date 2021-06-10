@props([
    'class' => '',
    'optionClass' => '',
    'title',
    'type' => false,
])
<div class="md:border-theme-secondary-300 md:dark:border-theme-secondary-800 {{ $class }}"@if($type) x-show="searchType === '{{ $type }}'"@endif>
    <div class=" flex flex-col max-w-full px-8 py-8 border-b border-theme-secondary-300 dark:border-theme-secondary-800 md:py-0 md:my-6 md:border-b-0 xl:flex-1  {{ $optionClass }}">
        <div class="text-sm font-semibold">{{ $title }}</div>
        <div class="flex items-center">
            {{ $slot }}
        </div>
    </div>
</div>
