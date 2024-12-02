@props([
    'model',
    'wallet' => null,
    'withoutLink' => false,
    'alwaysShowAddress' => false,
    'withoutTruncate' => false,
    'generic' => false,
])

@php ($isSent = $wallet && $model->isSent($wallet->address()) && ! $model->isSentToSelf($wallet->address()))
@php ($isSentToSelf = $wallet && $model->isSentToSelf($wallet->address()))

<div {{ $attributes->class('flex items-center space-x-2 text-sm font-semibold') }}>
    <div @class([
        'w-[47px] h-[21px] rounded border text-center leading-5 text-xs',
        'text-theme-secondary-700 bg-theme-secondary-200 border-theme-secondary-200 dark:bg-transparent dark:border-theme-dark-700 dark:text-theme-dark-200 encapsulated-badge' => $isSentToSelf,
        'text-theme-success-700 border-theme-success-100 dark:border-theme-success-700 dark:text-theme-success-500 bg-theme-success-100 dark:bg-transparent' => ! $isSent && ! $generic && ! $isSentToSelf,
        'text-theme-orange-dark border-theme-orange-light dark:border-theme-failed-state-bg dim:border-theme-failed-state-bg dark:text-theme-failed-state-text dim:text-theme-failed-state-text bg-theme-orange-light dark:bg-transparent' => $isSent && ! $generic && ! $isSentToSelf,
        'bg-theme-secondary-200 border-theme-secondary-200 dark:bg-transparent dark:border-theme-dark-700 dark:text-theme-dark-200'=> $generic && ! $isSentToSelf,
    ])>
        @if ($isSentToSelf)
            @lang('tables.transactions.return')
        @elseif ($isSent)
            @lang('tables.transactions.to')
        @else
            @lang('tables.transactions.from')
        @endif
    </div>

    <div>
        @if ($model->isTransfer() || $alwaysShowAddress)
            @php ($transactionWallet = $model->sender())
            @if ($isSent)
                @php ($transactionWallet = $model->recipient())
            @endif

            @unless ($withoutLink)
                <a
                    class="link"
                    href="{{ route('wallet', $transactionWallet->address()) }}"
                >
                    @if ($withoutTruncate)
                        {{ $transactionWallet->address }}
                    @else
                        <x-truncate-middle>{{ $transactionWallet->address }}</x-truncate-middle>
                    @endif
                </a>
            @else
                <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                    @if ($withoutTruncate)
                        {{ $transactionWallet->address }}
                    @else
                        <x-truncate-middle>{{ $transactionWallet->address }}</x-truncate-middle>
                    @endif
                </span>
            @endunless
        @else
            <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                @lang('tables.transactions.contract')
            </span>
        @endif
    </div>
</div>
