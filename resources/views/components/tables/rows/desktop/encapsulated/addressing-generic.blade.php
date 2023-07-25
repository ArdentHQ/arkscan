@props([
    'model',
])

@php ($transactionWallet = $model->sender())

<div class="flex flex-col space-y-2 text-sm font-semibold sm:space-y-1 md:space-y-2 md-lg:items-center md-lg:flex-row md-lg:space-y-0 md-lg:space-x-9">
    <div class="flex items-center space-x-2 md-lg:w-[156px]">
        <div @class([
            'w-[39px] h-[21px] rounded border border-transparent dark:border-theme-secondary-800 bg-theme-secondary-200 dark:bg-transparent text-center leading-5 text-xs',
        ])>
            @lang('tables.transactions.from')
        </div>

        <div>
            <a
                class="link"
                href="{{ route('wallet', $transactionWallet->address()) }}"
            >
                @if ($transactionWallet->isDelegate())
                    {{ $transactionWallet->username() }}
                @else
                    <x-truncate-middle>{{ $transactionWallet->address }}</x-truncate-middle>
                @endif
            </a>
        </div>
    </div>

    <div class="flex items-center space-x-2">
        <div @class([
            'w-[39px] h-[21px] rounded border border-transparent dark:border-theme-secondary-800 bg-theme-secondary-200 dark:bg-transparent text-center leading-5 text-xs',
        ])>
            @lang('tables.transactions.to')
        </div>

        <div>
            @if ($model->isTransfer())
                @php ($recipient = $model->sender())

                <a
                    class="link"
                    href="{{ route('wallet', $recipient->address()) }}"
                >
                    @if ($recipient->isDelegate())
                        {{ $recipient->username() }}
                    @else
                        <x-truncate-middle>{{ $recipient->address }}</x-truncate-middle>
                    @endif
                </a>
            @elseif ($model->isMultiPayment())
                <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                    @lang('tables.transactions.multiple')

                    ({{ count($model->payments()) }})
                </span>
            @else
                <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                    @lang('tables.transactions.contract')
                </span>
            @endif
        </div>
    </div>
</div>