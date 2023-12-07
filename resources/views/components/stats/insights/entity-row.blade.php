@props([
    'key',
    'model',
    'showTransactionCount' => false,
    'showFee' => false,
])

@php ($isTransaction = is_a($model, \App\ViewModels\TransactionViewModel::class))
@php ($isBlock = is_a($model, \App\ViewModels\BlockViewModel::class))

<div class="flex flex-col sm:flex-row sm:justify-between pt-3 md:pt-1 xl:pt-0 font-semibold md:flex-row first:pt-0">
    <div class="md:w-[209px] hidden md:inline">
        <span class="">
            @lang('pages.statistics.insights.transactions.header.'.$key)
        </span>
    </div>

    @if (! $isTransaction && ! $isBlock)
        <div class="md:w-50">
            @lang('general.na')
        </div>

        <div class="hidden xl:block xl:w-[227px]"></div>
        <div class="hidden xl:block xl:w-[180px]"></div>
    @else
        <div class="sm:flex-1 md:flex-none space-y-3 sm:space-y-0 sm:flex md:flex-col md:space-y-3 md-lg:flex-row md-lg:space-y-0 sm:justify-between md-lg:space-x-[66px]">
            <div class="space-y-2 md:space-y-0">
                <span class="md:hidden">
                    @lang('pages.statistics.insights.transactions.header.mobile.'.$key)
                </span>

                <x-stats.insights.entity-column class="md:w-[260px] md-lg:w-50">
                    <x-slot name="title">
                        @if ($isTransaction)
                            @lang('pages.statistics.insights.transactions.header.transaction_id')
                        @elseif ($isBlock)
                            @lang('pages.statistics.insights.transactions.header.block')
                        @endif
                    </x-slot>

                    <div class="hidden md:block">
                        @if ($isTransaction)
                            <a
                                href="{{ route('transaction', $model->model()) }}"
                                class="link"
                            >
                                <x-truncate-middle>{{ $model->id() }}</x-truncate-middle>
                            </a>
                        @elseif ($isBlock)
                            <a
                                href="{{ route('block', $model->model()) }}"
                                class="link"
                            >
                                <x-number>{{ $model->height() }}</x-number>
                            </a>
                        @endif
                    </div>

                    <div class="md:hidden">
                        <a
                            href="{{ route($isTransaction ? 'transaction' : 'block', $model->model()) }}"
                            class="link"
                        >
                            @if ($showTransactionCount)
                                {{ $model->transactionCount() }}
                            @elseif ($showFee)
                                {{ ExplorerNumberFormatter::currencyWithDecimals($model->fee(), Network::currency(), 2) }}
                            @else
                                {{ ExplorerNumberFormatter::currencyWithDecimals($model->amount(), Network::currency(), 2) }}
                            @endif
                        </a>
                    </div>
                </x-stats.insights.entity-column>
            </div>

            <div class="sm:flex md:flex-col md:space-y-3 xl:flex-row xl:space-y-0 sm:justify-between xl:space-x-[82px]">
                <x-stats.insights.entity-column class="hidden md:block md:w-[260px] xl:w-[227px]">
                    <x-slot name="title">
                        @if ($showTransactionCount)
                            @lang('pages.statistics.insights.transactions.header.transactions')
                        @else
                            @lang('pages.statistics.insights.transactions.header.amount')
                        @endif
                    </x-slot>

                    @if ($showTransactionCount)
                        {{ $model->transactionCount() }}
                    @elseif ($showFee)
                        {{ ExplorerNumberFormatter::currencyWithDecimals($model->fee(), Network::currency(), 2) }}
                    @else
                        {{ ExplorerNumberFormatter::currencyWithDecimals($model->amount(), Network::currency(), 2) }}
                    @endif
                </x-stats.insights.entity-column>

                <x-stats.insights.entity-column
                    class="sm:w-[90px] md:w-[260px] xl:w-[180px]"
                    :title="trans('pages.statistics.insights.transactions.header.date')"
                    show-on-mobile
                >
                    {{ $model->dateTime()->format('d M Y') }}
                </x-stats.insights.entity-column>
            </div>
        </div>
    @endif
</div>
