@props(['block'])

<div class="space-y-2 pt-4">
    <div class="flex items-center space-x-2">
        <div>@lang('general.search.block')</div>

        <a href="{{ $block->url() }}" class="min-w-0 link">
            <x-truncate-dynamic>
                {{ $block->id() }}
            </x-truncate-dynamic>
        </a>
    </div>

    <div class="flex flex-col md:items-center md:flex-row md:space-x-4 space-y-2 md:space-y-0">
        <div class="flex items-center space-x-2 text-xs">
            <div class="text-theme-secondary-500">
                @lang('general.search.generated_by')
            </div>

            <x-general.identity
                :model="$block->delegate()"
                without-reverse
                without-truncate
                without-reverse-class="space-x-2"
                :link-wallet="false"
            >
                <x-slot name="icon">
                    <x-general.avatar-small
                        :identifier="$block->address()"
                        size="w-5 h-5"
                    />
                </x-slot>
            </x-general.identity>
        </div>

        <div class="flex items-center space-x-1 text-xs">
            <div class="text-theme-secondary-500">
                @lang('general.search.transactions')
            </div>

            <div>
                {{ $block->transactionCount() }}
            </div>
        </div>
    </div>
</div>
