<div class="bg-white border-t-20 border-theme-secondary-100 dark:border-black dark:bg-theme-secondary-900">
    <div class="flex-wrap py-16 space-x-4 content-container md:px-8">
        <div class="w-full mb-8">
            <h2 class="text-3xl sm:text-4xl">@lang('pages.wallet.delegate.title', [$wallet->username()])</h2>
        </div>

        <div class="flex flex-wrap w-full divide-y divide-dashed divide-theme-secondary-300 dark:divide-theme-secondary-800">
            @if(Network::usesMarketSquare())
                <div class="grid w-full grid-flow-row grid-cols-1 gap-6 pt-8 mb-8 md:grid-cols-2 lg:grid-cols-4 gap-y-12 md:gap-y-4">
                    <x-details-box :title="trans('pages.wallet.delegate.rank')" icon="app-volume" icon-wrapper-class="bg-theme-danger-200" icon-class="text-theme-danger-400">
                        {{ $wallet->rank() }}/{{ Network::confirmations() }}
                    </x-details-box>

                    <x-details-box :title="trans('pages.wallet.delegate.commission')" icon="app-volume" icon-wrapper-class="bg-theme-danger-200" icon-class="text-theme-danger-400">
                        {{ $wallet->commission() }}
                    </x-details-box>

                    <x-details-box :title="trans('pages.wallet.delegate.payout_frequency')" icon="app-volume" icon-wrapper-class="bg-theme-danger-200" icon-class="text-theme-danger-400">
                        {{ $wallet->payoutFrequency() }}
                    </x-details-box>

                    <x-details-box :title="trans('pages.wallet.delegate.payout_minimum')" icon="app-volume" icon-wrapper-class="bg-theme-danger-200" icon-class="text-theme-danger-400">
                        {{ $wallet->payoutMinimum() }}
                    </x-details-box>
                </div>
            @endif

            <div class="grid w-full grid-flow-row grid-cols-1 gap-6 pt-8 md:grid-cols-2 lg:grid-cols-4 gap-y-12 md:gap-y-4">
                <x-details-box :title="trans('pages.wallet.delegate.forged_total')" icon="app-volume">
                    {{ $wallet->amountForged() }}
                </x-details-box>

                <x-details-box icon="app-volume">
                    <x-slot name="title">
                        @lang('pages.wallet.delegate.votes', [App\Services\NumberFormatter::percentage($wallet->votesPercentage())])
                    </x-slot>

                    {{ $wallet->votes() }} <a href="{{ route('wallet.voters', $wallet->address()) }}" class="link">@lang('general.see_all')</a>
                </x-details-box>

                <x-details-box :title="trans('pages.wallet.delegate.forged_blocks')" icon="app-volume">
                    {{ $wallet->forgedBlocks() }} <a href="{{ route('wallet.blocks', $wallet->address()) }}" class="link">@lang('general.see_all')</a>
                </x-details-box>

                <x-details-box :title="trans('pages.wallet.delegate.productivity')" icon="app-volume">
                    {{ $wallet->productivity() }}
                </x-details-box>
            </div>
        </div>
    </div>
</div>
