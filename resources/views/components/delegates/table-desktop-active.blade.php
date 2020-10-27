<div class="hidden w-full table-container md:block">
    <table>
        <thead>
            <tr>
                <th>@lang('general.delegates.rank')</th>
                <th><span class="pl-14">@lang('general.delegates.name')</span></th>
                <th><span class="pl-14">@lang('general.delegates.status')</span></th>
                <th>@lang('general.delegates.votes')</th>
                @if (Network::usesMarketSquare())
                <th>@lang('general.delegates.profile')</th>
                <th>@lang('general.delegates.commission')</th>
                @endif
                <th width="120" class="hidden text-right lg:table-cell">@lang('general.delegates.productivity')</th>
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
                    <td>
                        <div wire:loading.class="h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                        <div wire:loading.class="hidden">
                            <div class="flex flex-row items-center space-x-3 pl-14">
                                @foreach($delegate->performance() as $performed)
                                    @if($performed)
                                        <span class="text-theme-success-500">
                                            @svg('app-status-done', 'w-8 h-8')
                                        </span>
                                    @else
                                        <span class="text-theme-danger-500">
                                            @svg('app-status-undone', 'w-8 h-8')
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </td>
                    <td>
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
                    <td class="hidden text-right lg:table-cell">
                        <div wire:loading.class="h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                        <div wire:loading.class="hidden">{{ $delegate->productivity() }}</div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
