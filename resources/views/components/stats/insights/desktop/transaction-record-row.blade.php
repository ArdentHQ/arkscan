@props([
    'key',
    'model',
])

@php ($isTransaction = is_a($model, \App\ViewModels\TransactionViewModel::class))
@php ($isBlock = is_a($model, \App\ViewModels\BlockViewModel::class))

<div class="hidden pt-1 w-full min-w-0 md:flex xl:pt-0">
    @if (! $isTransaction && ! $isBlock)
        <div class="flex justify-between w-full xl:w-[770px]">
            <div class="flex flex-1">
                @lang('pages.statistics.insights.transactions.header.'.$key)
            </div>
            <div class="md:w-50">
                @lang('general.na')
            </div>
        </div>
    @else
        <div class="flex justify-between w-full xl:w-[770px]">
            <div class="flex flex-1">
                @lang('pages.statistics.insights.transactions.header.'.$key)
            </div>
            <div class="flex flex-col flex-1 justify-between space-y-3 md-lg:flex-2 md-lg:flex-row md-lg:space-y-0">
                <div class="flex flex-1 justify-between">
                    <span>
                        @if ($isTransaction)
                            @lang('pages.statistics.insights.transactions.header.transaction_id'):
                        @elseif ($isBlock)
                            @lang('pages.statistics.insights.transactions.header.block'):
                        @endif
                    </span>
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
                <div class="flex flex-col flex-1 space-y-3 w-full xl:flex-row xl:space-y-0 md-lg:pl-16">
                    <div class="flex flex-1 justify-between space-x-2 w-full">
                        <div>
                            @if ($key === 'most_transactions_in_block')
                                @lang('pages.statistics.insights.transactions.header.transactions'):
                            @else
                                @lang('pages.statistics.insights.transactions.header.amount'):
                            @endif
                        </div>
                        <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                            @if ($key === 'most_transactions_in_block')
                                {{ $model->transactionCount() }}
                            @elseif ($key === 'highest_fee')
                                {{ ExplorerNumberFormatter::currencyWithDecimals($model->fee(), Network::currency(), 2) }}
                            @else
                                {{ ExplorerNumberFormatter::currencyForViews($model->amount(), Network::currency()) }}
                            @endif
                        </div>
                    </div>

                    <div class="hidden justify-between space-x-2 md:flex xl:hidden">
                        <div>
                            @lang('pages.statistics.insights.transactions.header.date'):
                        </div>
                        <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                            {{ $model->dateTime()->format('d M Y') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-between space-x-2 md:hidden xl:flex md-lg:pl-16 xl:w-[330px]">
            <div>
                @lang('pages.statistics.insights.transactions.header.date'):
            </div>
            <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                {{ $model->dateTime()->format('d M Y') }}
            </div>
        </div>
    @endif
</div>
