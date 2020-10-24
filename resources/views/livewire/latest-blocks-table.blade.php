<div id="block-list" class="w-full">
    <x-blocks.table-desktop :blocks="$blocks" />
    <x-blocks.list-mobile :blocks="$blocks" />

    <div class="pt-4 mt-8 border-t border-theme-secondary-300 dark:border-theme-secondary-800 md:mt-0 md:border-dashed">
        <a href="{{ route('blocks') }}" class="w-full button-secondary">@lang('actions.view_all')</a>
    </div>
</div>
