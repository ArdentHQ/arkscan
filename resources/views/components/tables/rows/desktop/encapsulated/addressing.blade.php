@props([
    'model',
    'wallet' => null,
    'withoutLink' => false,
])

@php ($isSent = $wallet && $model->isSent($wallet->address()))

<div class="flex items-center space-x-2 text-sm font-semibold">
    <div @class([
        'w-[39px] h-[21px] rounded border text-center leading-5 text-xs',
        'text-theme-success-700 border-theme-success-100 dark:border-theme-success-700 dark:text-theme-success-500 bg-theme-success-100 dark:bg-transparent' => ! $isSent,
        'text-theme-orange-dark border-theme-orange-light dark:border-[#AA6868] dark:text-[#F39B9B] bg-theme-orange-light dark:bg-transparent' => $isSent,
    ])>
        @if ($isSent)
            @lang('tables.transactions.to')
        @else
            @lang('tables.transactions.from')
        @endif
    </div>

    <div>
        @if ($model->isTransfer())
            @php ($transactionWallet = $model->sender())
            @if ($isSent)
                @php ($transactionWallet = $model->recipient())
            @endif

            @unless ($withoutLink)
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
            @else
                <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                    @if ($transactionWallet->isDelegate())
                        {{ $transactionWallet->username() }}
                    @else
                        <x-truncate-middle>{{ $transactionWallet->address }}</x-truncate-middle>
                    @endif
                </span>
            @endunless
        @elseif ($model->isMultiPayment())
            @if ($isSent)
                <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                    @lang('tables.transactions.multiple')

                    ({{ count($model->payments()) }})
                </span>
            @else
                <a
                    class="link"
                    href="{{ route('wallet', $model->sender()->address()) }}"
                >
                    @if ($model->sender()->isDelegate())
                        {{ $model->sender()->username() }}
                    @else
                        <x-truncate-middle>{{ $model->sender()->address }}</x-truncate-middle>
                    @endif
                </a>
            @endif
        @else
            <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                @lang('tables.transactions.contract')
            </span>
        @endif
    </div>
</div>
