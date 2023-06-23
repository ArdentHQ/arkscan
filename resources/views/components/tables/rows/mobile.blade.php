@props(['header'])

<div {{ $attributes->class('text-sm rounded border border-theme-secondary-300 dark:border-theme-dark-700') }}>
    <div {{ $header->attributes->class([
        'flex justify-between items-center rounded-t bg-theme-secondary-100 dark:bg-theme-dark-950',
        $header->attributes->get('padding', 'py-3 px-4')
    ]) }}>
        {{ $header }}
    </div>

    <div class="flex flex-col px-4 pt-3 pb-4 space-y-4 sm:flex-row sm:space-y-0">
        {{ $slot }}
    </div>
</div>
