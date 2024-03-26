@props([
    'model',
])

@php ($transactionWallet = $model->sender())

<div class="flex flex-col space-y-2 text-sm font-semibold sm:space-y-1 md:space-y-2 md-lg:items-center md-lg:flex-row md-lg:space-y-0 md-lg:space-x-9">
    <div class="flex items-center space-x-2 md-lg:w-41">
        <x-general.badge class="text-center encapsulated-badge w-[39px]">
            @lang('tables.transactions.from')
        </x-general.badge>

        <x-dynamic-tooltip tooltip="{{$transactionWallet->isValidator() ? $transactionWallet->username() : null}}">
            <div
                class="min-w-0 truncate"
            >
                <a
                    class="whitespace-nowrap link"
                    href="{{ route('wallet', $transactionWallet->address()) }}"
                >
                    @if ($transactionWallet->isValidator())
                        {{ $transactionWallet->username() }}
                    @else
                        <x-truncate-middle>{{ $transactionWallet->address }}</x-truncate-middle>
                    @endif
                </a>
            </div>
        </x-dynamic-tooltip>
    </div>

    <div class="flex items-center space-x-2">
        <x-general.badge class="text-center encapsulated-badge w-[39px]">
            @lang('tables.transactions.to')
        </x-general.badge>

        @php ($recipient = $model->recipient())

        <x-dynamic-tooltip tooltip="{{$model->isTransfer() && $recipient->isValidator() ? $recipient->username() : null}}">
            <div
                class="min-w-0 truncate"
            >
                @if ($model->isTransfer())
                    <a
                        class="whitespace-nowrap link"
                        href="{{ route('wallet', $recipient->address()) }}"
                    >
                        @if ($recipient->isValidator())
                            {{ $recipient->username() }}
                        @else
                            <x-truncate-middle>{{ $recipient->address }}</x-truncate-middle>
                        @endif
                    </a>
                @elseif ($model->isMultiPayment())
                    <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                        @lang('tables.transactions.multiple')

                        ({{ $model->recipientsCount() }})
                    </span>
                @else
                    <span class="text-theme-secondary-900 dark:text-theme-dark-50">
                        @lang('tables.transactions.contract')
                    </span>
                @endif
            </div>
        </x-dynamic-tooltip>
    </div>
</div>
