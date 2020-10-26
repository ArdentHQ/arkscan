<div class="space-y-8 divide-y table-list-mobile">
    @foreach ($delegates as $delegate)
        <div class="table-list-mobile-row">
            <table>
                <tr>
                    <td>@lang('pages.monitor.order')</td>
                    <td>
                        <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
                        <div wire:loading.class="hidden">{{ $delegate['order'] }}</div>
                    </td>
                </tr>
                <tr>
                    <td width="100">@lang('pages.monitor.name')</td>
                    <td>
                        <div class="flex flex-row items-center space-x-3">
                            <div wire:loading.class="h-6 rounded-full w-11 bg-theme-secondary-300 animate-pulse"></div>
                            <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
                        </div>

                        <x-general.address :address="$delegate['username']" />
                    </td>
                </tr>
                <tr>
                    <td>@lang('pages.monitor.forging_at')</td>
                    <td>
                        <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
                        <div wire:loading.class="hidden">{{ $delegate['forging_at'] }}</div>
                    </td>
                </tr>
                <tr>
                    <td>@lang('pages.monitor.status')</td>
                    <td>
                        <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
                        <div wire:loading.class="hidden">{{ $delegate['status'] }}</div>
                    </td>
                </tr>
                <tr>
                    <td>@lang('pages.monitor.block_id')</td>
                    <td>
                        <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
                        <div wire:loading.class="hidden">{{ $delegate['last_block'] }}</div>
                    </td>
                </tr>
            </table>
        </div>
    @endforeach
</div>
