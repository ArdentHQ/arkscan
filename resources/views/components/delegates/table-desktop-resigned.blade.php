<div class="hidden w-full table-container md:block">
    <table>
        <thead>
            <tr>
                <th width="50">@lang('general.transaction.id')</th>
                <th><span class="pl-14">@lang('general.delegates.name')</span></th>
                <th width="200" class="hidden text-right lg:table-cell">@lang('general.delegates.votes')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($delegates as $delegate)
                <tr>
                    <td>
                        <div class="flex items-center">
                            <x-general.loading-state.icon icon="link" class="mx-auto" />

                            <a href="{{ route('transaction', $delegate->resignationId()) }}" class="mx-auto link" wire:loading.class="hidden">
                                @svg('link', 'h-4 w-4')
                            </a>
                        </div>
                    </td>
                    <td>
                        <div class="flex flex-row items-center space-x-3">
                            <div wire:loading.class="h-6 rounded-full w-11 bg-theme-secondary-300 animate-pulse"></div>
                            <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
                        </div>

                        <x-general.address :address="$delegate->username()" />
                    </td>
                    <td class="hidden text-right lg:table-cell">
                        <div wire:loading.class="h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>
                        <div wire:loading.class="hidden">{{ $delegate->votes() }} <span>{{ $delegate->votesPercentage() }}</span></div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
