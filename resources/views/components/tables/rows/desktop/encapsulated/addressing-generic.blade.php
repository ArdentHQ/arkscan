@props([
    'model',
])

@php ($transactionWallet = $model->sender())

<div class="flex flex-col space-y-2 text-sm font-semibold sm:space-y-1 md:space-y-2 md-lg:items-center md-lg:flex-row md-lg:space-y-0 md-lg:space-x-9">
    <div class="flex items-center space-x-2 md-lg:w-41">
        <x-general.badge class="text-center encapsulated-badge w-[39px]">
            @lang('tables.transactions.from')
        </x-general.badge>

        <div class="min-w-0 truncate">
            <a
                class="whitespace-nowrap link"
                href="{{ route('wallet', $transactionWallet->address()) }}"
            >
                <x-truncate-middle>{{ $transactionWallet->address }}</x-truncate-middle>
            </a>
        </div>
    </div>

    <div class="flex items-center space-x-2">
        <x-general.badge class="text-center encapsulated-badge w-[39px]">
            @lang('tables.transactions.to')
        </x-general.badge>

        @php ($recipient = $model->recipient())

        <div
            class="min-w-0 truncate"
        >
            @if ($model->isTransfer() || $model->isTokenTransfer())
                <a
                    class="whitespace-nowrap link"
                    href="{{ route('wallet', $recipient->address()) }}"
                >
                    <x-truncate-middle>{{ $recipient->address }}</x-truncate-middle>
                </a>
            @else
                <a
                    class="whitespace-nowrap link"
                    href="{{ route('wallet', $recipient->address()) }}"
                >
                    @lang('tables.transactions.contract')
                </a>
            @endif
        </div>
    </div>
</div>
