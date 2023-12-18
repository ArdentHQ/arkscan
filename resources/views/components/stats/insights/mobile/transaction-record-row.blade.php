@props([
    'key',
    'model',
])

@php ($isTransaction = is_a($model, \App\ViewModels\TransactionViewModel::class))
@php ($isBlock = is_a($model, \App\ViewModels\BlockViewModel::class))

<div class="flex md:hidden">
    <div class="flex flex-col justify-between pt-3 space-y-3 w-full sm:flex-row sm:space-y-0">
        @if (! $isTransaction && ! $isBlock)
            <div class="flex flex-col space-y-2">
                <span>
                    @lang('pages.statistics.insights.transactions.header.mobile.'.$key)
                </span>
                <div class="md:w-50">
                    @lang('general.na')
                </div>
            </div>
        @else
            <div class="flex flex-col space-y-2">
                <span>
                    @lang('pages.statistics.insights.transactions.header.mobile.'.$key)
                </span>
                @if ($isTransaction)
                    <a
                        href="{{ route('transaction', $model->model()) }}"
                        class="link"
                    >
                        {{ trim(trim(ExplorerNumberFormatter::currencyWithDecimals($model->amount(), Network::currency(), 0), '0'), '.') }}
                    </a>
                @elseif ($isBlock)
                    <a
                        href="{{ route('block', $model->model()) }}"
                        class="link"
                    >
                        @if ($key === 'most_transactions_in_block')
                            {{ $model->transactionCount() }}
                        @elseif ($key === 'highest_fee')
                            {{ ExplorerNumberFormatter::currencyWithDecimals($model->fee(), Network::currency(), 2) }}
                        @else
                            {{ trim(trim(ExplorerNumberFormatter::currencyWithDecimals($model->amount(), Network::currency(), 0), '0'), '.') }}
                        @endif
                    </a>
                @endif
            </div>

            <div class="flex flex-col space-y-2 w-[90px]">
                <div>
                    @lang('pages.statistics.insights.transactions.header.date')
                </div>
                <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                    {{ $model->dateTime()->format('d M Y') }}
                </div>
            </div>
        @endif
    </div>
</div>
