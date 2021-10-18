<x-page-headers.wallet.frame title="pages.wallet.title" :wallet="$wallet">
    <x-page-headers.wallet.frame-item icon="wallet" title-class="whitespace-nowrap">
        <x-slot name="title">
            @lang('pages.wallet.balance')@if(Network::canBeExchanged()): <livewire:wallet-balance :wallet="$wallet->model()" /> @endif
        </x-slot>

        <x-currency :currency="Network::currency()">{{ $wallet->balance() }}</x-currency>
    </x-page-headers.wallet.frame-item>

    @if($wallet->isDelegate())
        @php
            $rank = $wallet->rank();
            $isResigned = $wallet->isResigned();
            $isStandby = (! $isResigned && $rank === 0) || $rank > Network::delegateCount();
            $vote = $wallet->vote()
        @endphp

        <x-slot name="extension">
            <div class="flex flex-col space-y-4 w-full lg:flex-row lg:justify-between lg:space-y-0">
                <div class="grid grid-cols-1 space-y-4 sm:grid-cols-3 sm:space-y-0 lg:flex lg:space-x-5">
                    <x-general.header-entry
                        title="{{ trans('pages.wallet.delegate.rank') }} / {{ trans('pages.wallet.delegate.status') }}"
                    >
                        <x-slot name="icon">
                            <div class="flex items-center md:mr-2">
                                <div class="hidden lg:flex">
                                    <div class="-mr-2 circled-icon  {{ $wallet->delegateRankStyling() }} dark:text-theme-secondary-600 dark:border-theme-secondary-600">
                                        <x-ark-icon name="app-rank" />
                                    </div>
                                </div>

                                <div class="hidden rounded-full border-4 md:flex border-theme-secondary-100 dark:border-theme-secondary-900">
                                    <div class="rounded-full circled-icon {{ $wallet->delegateStatusStyling() }} bg-theme-secondary-100 dark:bg-theme-secondary-900">
                                        <x-ark-icon :name="$isStandby ? 'clock' : 'checkmark-smooth'" />
                                    </div>
                                </div>
                            </div>
                        </x-slot>

                        <x-slot name="text">
                            @if(! $isResigned)
                                {{ $rank === 0 ? '-' : trans('pages.wallet.vote_rank', [$rank]) }} /
                            @endif
                            @if($isResigned)
                                <span class="text-theme-danger-400">@lang('pages.delegates.resigned')</span>
                            @elseif($isStandby)
                                <span class="text-theme-secondary-500 dark:text-theme-secondary-700">@lang('pages.delegates.standby')</span>
                            @else
                                <span class="text-theme-success-600">@lang('pages.delegates.active')</span>
                            @endif
                        </x-slot>
                    </x-general.header-entry>

                    @if (! $isResigned)
                        <x-general.header-entry
                            :title="trans('pages.wallet.productivity')"
                            :tooltip="trans('pages.wallet.productivity_tooltip')"
                        >
                            <x-slot name="text">
                                <span @if($isStandby)class="text-theme-secondary-500 dark:text-theme-secondary-700" @endif>
                                    @if($wallet->productivity() >= 0)
                                        <x-percentage>
                                            {{ $wallet->productivity() }}
                                        </x-percentage>
                                    @else
                                        <span class="text-theme-secondary-500 dark:text-theme-secondary-700">
                                            @lang('generic.not-available')
                                        </span>
                                    @endif
                                </span>
                            </x-slot>
                        </x-general.header-entry>
                    @endif

                    <x-general.header-entry
                        :title="trans('pages.wallet.delegate.forged_total')"
                        without-border
                    >
                        <x-slot name="text">
                            <span @if($isResigned)class="text-theme-secondary-500 dark:text-theme-secondary-700" @endif>
                                <x-number>{{ $wallet->totalForged() }}</x-number>
                                {{ Network::currency() }}
                            </span>
                        </x-slot>
                    </x-general.header-entry>
                </div>

                <div class="grid grid-cols-1 space-y-4 sm:grid-cols-3 sm:space-y-0 lg:flex lg:space-x-5">
                    <x-general.header-entry
                        :title="trans('pages.wallet.delegate.forged_blocks')"
                        :text="trans('general.see_all')"
                        :url="route('wallet.blocks', $wallet->address())"
                        text-alignment="lg:text-right"
                    >
                        <x-slot name="icon">
                            <div class="md:mr-4 md:w-11 lg:mr-0 lg:w-0"></div>
                        </x-slot>
                    </x-general.header-entry>

                    <x-general.header-entry
                        :title="trans('pages.wallet.delegate.votes', [ExplorerNumberFormatter::currencyShortNotation($wallet->votes())])"
                        :text="trans('general.see_all')"
                        :url="route('wallet.voters', $wallet->address())"
                        wrapper-class="sm:mr-7 lg:mr-0"
                        without-border
                        text-alignment="lg:text-right"
                    />
                </div>
            </div>
        </x-slot>
    @endif
</x-page-headers.wallet.frame>
