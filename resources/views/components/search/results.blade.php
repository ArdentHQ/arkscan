<div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
    <div class="flex-col py-16 content-container md:px-8">
        <h1 class="header-2">@lang('pages.search_results.title')</h1>

        <div class="mt-4">
            @if ($type === 'transaction')
                <livewire:transaction-table />
            @elseif ($type === 'block')
                <livewire:block-table />
            @else
                <livewire:wallet-table />
            @endif
        </div>
    </div>
</div>
