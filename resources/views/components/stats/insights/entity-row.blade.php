@props([
    'key',
    'model',
    'showTransactionCount' => false,
])

@php ($isTransaction = is_a($model, \App\ViewModels\TransactionViewModel::class))
@php ($isBlock = is_a($model, \App\ViewModels\BlockViewModel::class))

<div class="flex flex-col space-y-2 md:space-y-0 md:flex-row font-semibold md:justify-between pt-3 first:pt-0 md:pt-0">
    <div class="md:w-[209px]">
        <span class="hidden md:inline">
            @lang('pages.statistics.insights.transactions.header.'.$key)
        </span>

        <span class="md:hidden">
            @lang('pages.statistics.insights.transactions.header.mobile.'.$key)
        </span>
    </div>

    @if (! $isTransaction && ! $isBlock)
        <div class="md:w-50">
            @lang('general.na')
        </div>

        <div class="hidden md:block md:w-[227px]"></div>
        <div class="hidden md:block md:w-[180px]"></div>
    @else
        <x-stats.insights.entity-column class="md:w-50">
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
                    @else
                        {{ ExplorerNumberFormatter::currencyWithDecimals($model->fee(), Network::currency(), 2) }}
                    @endif
                </a>
            </div>
        </x-stats.insights.entity-column>

        <x-stats.insights.entity-column class="hidden md:block md:w-[227px]">
            <x-slot name="title">
                @if ($showTransactionCount)
                    @lang('pages.statistics.insights.transactions.header.transactions')
                @else
                    @lang('pages.statistics.insights.transactions.header.amount')
                @endif
            </x-slot>

            @if ($showTransactionCount)
                {{ $model->transactionCount() }}
            @else
                {{ ExplorerNumberFormatter::currencyWithDecimals($model->fee(), Network::currency(), 2) }}
            @endif
        </x-stats.insights.entity-column>

        <x-stats.insights.entity-column
            class="md:w-[180px] pt-1 md:pt-0"
            :title="trans('pages.statistics.insights.transactions.header.date')"
            show-on-mobile
        >
            {{ $model->dateTime()->format('d M Y') }}
        </x-stats.insights.entity-column>
    @endif
</div>
