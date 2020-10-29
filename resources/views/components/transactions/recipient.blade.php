@php($iconType = $transaction->iconType())

<div wire:key="{{ $transaction->id() }}">
    @php($address = null)
    @php($username = null)
    @php($text = trans('general.transaction.'.$iconType))

    <div @if ($withLoading ?? false) wire:loading.class="hidden" @endif>
        @if ($transaction->isTransfer() || $transaction->isUnknown())
            <x-general.address :model="$transaction->recipient()" />
        @elseif ($transaction->isVote())
            <x-general.address :model="$transaction->voted()">
                <x-slot name="icon">
                    <x-transactions.icon :icon-type="$iconType" />
                </x-slot>

                <x-slot name="prefix">
                    <span class="pr-2 mr-2 font-semibold border-r text-theme-secondary-900 dark:text-theme-secondary-200 border-theme-secondary-300 dark:border-theme-secondary-800">
                        @lang('general.transaction.vote')
                    </span>
                </x-slot>
            </x-general.address>
        @elseif ($transaction->isUnvote())
            <x-general.address :model="$transaction->unvoted()">
                <x-slot name="icon">
                    <x-transactions.icon :icon-type="$iconType" />
                </x-slot>

                <x-slot name="prefix">
                    <span class="pr-2 mr-2 font-semibold border-r text-theme-secondary-900 dark:text-theme-secondary-200 border-theme-secondary-300 dark:border-theme-secondary-800">
                        @lang('general.transaction.unvote')
                    </span>
                </x-slot>
            </x-general.address>
        @else
            <div class="flex items-center space-x-3">
                <x-transactions.icon :icon-type="$iconType" />

                <div class="font-semibold text-theme-secondary-900 dark:text-theme-secondary-200">
                    {{ $text }}
                </div>
            </div>
        @endif
    </div>

    @if ($withLoading ?? false)
        <x-general.loading-state.recipient-address :address="$address" :username="$username" :text="$text" />
    @endif
</div>
