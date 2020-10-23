<div class="pagination-wrapper">
    <div class="pagination-pages-mobile">
        <button type="button" class="button-pagination-page-indicator" disabled><span>Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span></button>
    </div>

    <div class="flex space-x-3">
        <button
            wire:click="gotoPage(1)"
            class="items-center button-secondary pagination-button-mobile" @if($paginator->onFirstPage()) disabled @endif
        >
            <span>@svg('pagination-first', 'h-3 w-3')</span>
        </button>
        <button
            wire:click="gotoPage({{ $paginator->currentPage() - 1 }})"
            class="items-center button-secondary pagination-button-mobile" @if($paginator->onFirstPage()) disabled @endif
        >
            <div class="flex items-center">@svg('chevron-left', 'h-3 w-3')<span class="hidden lg:flex lg:ml-2">Previous</span></div>
        </button>

        <div class="hidden px-2 rounded bg-theme-primary-100 dark:bg-theme-secondary-800 md:flex flex-inline">
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <button type="button" class="button-pagination-page-indicator" disabled><span>{{ $element }}</span></button>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <button
                            class="@if ($paginator->currentPage() === $page) button-pagination-page-indicator--selected @else button-pagination-page-indicator  @endif"
                            wire:click="gotoPage({{ $page }})"
                        >
                            {{ $page }}
                        </button>
                    @endforeach
                @endif
            @endforeach
        </div>

        <div class="pagination-pages md:hidden">
            <button type="button" class="button-pagination-page-indicator" disabled><span>Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span></button>
        </div>

        <button
            wire:click="gotoPage({{ $paginator->currentPage() + 1 }})"
            class="items-center button-secondary pagination-button-mobile" @if($paginator->currentPage() === $paginator->lastPage()) disabled @endif
        >
            <div class="flex items-center"><span class="hidden lg:flex lg:mr-2">Next</span>@svg('chevron-right', 'h-3 w-3')</div>
        </button>
        <button
            wire:click="gotoPage({{ $paginator->lastPage() }})"
            class="items-center button-secondary pagination-button-mobile"
            @if($paginator->currentPage() === $paginator->lastPage()) disabled @endif
        >
            @svg('pagination-last', 'h-3 w-3')
        </button>
    </div>
</div>
