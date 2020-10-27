<div class="hidden w-full table-container md:block">
    <table>
        <thead>
            <tr>
                <th width="60">@lang('general.delegates.rank')</th>
                <th><span class="pl-14">@lang('general.delegates.name')</span></th>
                <th width="250" class="hidden text-right lg:table-cell">@lang('general.delegates.votes')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($delegates as $delegate)
                <tr>
                    <td>
                        <div wire:loading.class="h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                        <div wire:loading.class="hidden">{{ $delegate->rank() }}</div>
                    </td>
                    <td>
                        <div class="flex flex-row items-center space-x-3">
                            <div wire:loading.class="w-6 h-6 rounded-full md:w-11 md:h-11 bg-theme-secondary-300 animate-pulse"></div>
                            <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
                        </div>

                        <x-general.address :address="$delegate->username()" />
                    </td>
                    <td class="hidden text-right lg:table-cell">
                        <div wire:loading.class="h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                        <div wire:loading.class="hidden">{{ $delegate->votes() }} <span>{{ $delegate->votesPercentage() }}</span></div>
                    </td>
                    @if (Network::usesMarketSquare())
                    <td>
                        {{-- @TODO: MSQ Profile --}}
                    </td>
                    <td>
                        {{-- @TODO: MSQ Commission --}}
                    </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
