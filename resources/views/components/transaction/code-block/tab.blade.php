@props(['value'])

<div
    class="flex items-center h-full transition-default px-1.5 group/tab"
    :class="{
        'text-theme-primary-500 dark:text-theme-dark-blue-500 cursor-default': view === '{{ $value }}',
        'text-theme-secondary-500 hover:text-theme-secondary-50 dark:text-theme-dark-200 dark:hover:text-theme-dark-50 cursor-pointer': view !== '{{ $value }}',
    }"
    @click="changeView('{{ $value }}')"
>
    <div
        class="border-theme-primary-500 border-b-2 pb-2 sm:pb-0 flex items-center h-full transition-default"
        :class="{
            'border-theme-primary-500 dark:border-theme-dark-blue-500': view === '{{ $value }}',
            'border-transparent group-hover/tab:border-theme-secondary-700 dark:group-hover/tab:border-theme-dark-500': view !== '{{ $value }}',
        }"
    >
        <div class="sm:pt-[2px] leading-3.75">
            @lang('pages.transaction.code-block.tab.'.$value)
        </div>
    </div>
</div>
