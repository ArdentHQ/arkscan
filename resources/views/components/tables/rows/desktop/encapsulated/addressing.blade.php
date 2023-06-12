@props([
    'model',
    'isReceived' => false,
])

<div class="flex items-center space-x-2 text-sm font-semibold">
    <div @class([
        'w-[39px] h-[21px] rounded border text-center leading-5 text-xs',
        'text-theme-success-700 border-theme-success-100 dark:border-theme-success-700 bg-theme-success-100 dark:bg-transparent' => $isReceived,
        'text-theme-orange-dark border-theme-orange-light dark:border-theme-orange-dark bg-theme-orange-light dark:bg-transparent' => ! $isReceived,
    ])>
        @if ($isReceived)
            @lang('tables.transactions.from')
        @else
            @lang('tables.transactions.to')
        @endif
    </div>

    <div>
        @if ($model->isTransfer())
            @if ($isReceived)
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
            @else
                <a
                    class="link"
                    href="{{ route('wallet', $model->recipient()->address()) }}"
                >
                    @if ($model->recipient()->isDelegate())
                        {{ $model->recipient()->username() }}
                    @else
                        <x-truncate-middle>{{ $model->recipient()->address }}</x-truncate-middle>
                    @endif
                </a>
            @endif
        @elseif ($model->isMultiPayment())
            @if ($isReceived)
                <span class="text-theme-secondary-900 dark:text-theme-secondary-200">
                    @lang('tables.transactions.multiple')

                    ({{ count($model->payments()) }})
                </span>
            @else
                <span class="text-theme-secondary-900 dark:text-theme-secondary-200">
                    @lang('tables.transactions.multiple')

                    ({{ count($model->payments()) }})
                </span>
            @endif
        @else
            <span class="text-theme-secondary-900 dark:text-theme-secondary-200">
                @lang('tables.transactions.contract')
            </span>
        @endif
    </div>
</div>
