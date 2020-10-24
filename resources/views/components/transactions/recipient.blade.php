@php($iconType = $transaction->iconType())

<div wire:loading.class="hidden">
    @if ($transaction->isUnknown())
        <x-general.address :address="$transaction->recipient() ?? $transaction->sender()" />
    @elseif ($transaction->isTransfer())
        <x-general.address :address="$transaction->recipient() ?? $transaction->sender()" />
    @elseif ($transaction->isVote())
        <x-general.address :address="$transaction->voted()->address">
            <x-slot name="icon">
                <x-transactions.icon :icon-type="$iconType" />
            </x-slot>

            <x-slot name="prefix">
                <span class="pr-2 mr-2 font-semibold border-r border-theme-secondary-300 dark:border-theme-secondary-800">
                    @lang('general.transaction.vote')
                </span>
            </x-slot>
        </x-general.address>
    @elseif ($transaction->isUnvote())
        <x-general.address :address="$transaction->unvoted()->address">
            <x-slot name="icon">
                <x-transactions.icon :icon-type="$iconType" />
            </x-slot>

            <x-slot name="prefix">
                <span class="pr-2 mr-2 font-semibold border-r border-theme-secondary-300 dark:border-theme-secondary-800">
                    @lang('general.transaction.unvote')
                </span>
            </x-slot>
        </x-general.address>
    @else
        <div class="flex items-center space-x-3">
            <x-transactions.icon :icon-type="$iconType" />

            <div class="font-semibold">
                @lang('general.transaction.'.$iconType)
            </div>
        </div>
    @endif
</div>
