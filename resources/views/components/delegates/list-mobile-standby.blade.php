<div class="space-y-8 divide-y table-list-mobile md:hidden">
    @foreach ($delegates as $delegate)
        <div class="flex flex-col space-y-3 w-full pt-8 {{ $loop->first ? '' : 'border-t'}} border-theme-secondary-300">
            <div class="flex justify-between w-full">
                @lang('general.delegates.rank')

                <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
                <div wire:loading.class="hidden">{{ $delegate->rank() }}</div>
            </div>

            <div class="flex justify-between w-full">
                @lang('general.delegates.name')

                <div class="flex flex-row items-center space-x-3">
                    <div wire:loading.class="h-6 rounded-full w-11 bg-theme-secondary-300 animate-pulse"></div>
                    <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
                </div>

                <x-general.address :address="$delegate->username()" />
            </div>

            <div class="flex justify-between w-full">
                @lang('general.delegates.votes')

                <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
                <div wire:loading.class="hidden">
                    {{ $delegate->votes() }}

                    <span>{{ $delegate->votesPercentage() }}</span>
                </div>
            </div>

            @if (Network::usesMarketSquare())
                <div class="flex justify-between w-full">
                    @lang('general.delegates.profile')

                    {{--@TODO: MSQ Profile--}}
                </div>

                <div class="flex justify-between w-full">
                    @lang('general.delegates.commission')

                    {{--@TODO: MSQ Commission--}}
                </div>
            @endif
        </div>
    @endforeach
</div>
