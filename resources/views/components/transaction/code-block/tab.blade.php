@props(['value'])

<div
    class="flex items-center px-1.5 h-full transition-default group/tab first:pl-0 sm:first:pl-1.5"
    :class="{
        'text-theme-primary-500 dark:text-theme-dark-blue-500 cursor-default': view === '{{ $value }}',
        'text-theme-secondary-500 hover:text-theme-secondary-50 dark:text-theme-dark-200 dark:hover:text-theme-dark-50 cursor-pointer': view !== '{{ $value }}',
    }"
    @click="changeView('{{ $value }}')"
>
    <div
        class="flex items-center pb-2 h-full border-b-2 sm:pb-0 border-theme-primary-500 transition-default"
        :class="{
            'border-theme-primary-500 dark:border-theme-dark-blue-500': view === '{{ $value }}',
            'border-transparent group-hover/tab:border-theme-secondary-700 dark:group-hover/tab:border-theme-dark-500': view !== '{{ $value }}',
        }"
    >
        <div class="leading-3.75 sm:pt-[2px]">
            @lang('pages.transaction.code-block.tab.'.$value)
        </div>
    </div>
</div>
