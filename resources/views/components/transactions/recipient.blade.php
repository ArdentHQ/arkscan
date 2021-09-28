@php($iconType = $transaction->iconType())

<div>
    <div>
        @if ($transaction->isTransfer() || $transaction->isUnknown())
            <x-general.identity :model="$transaction->recipient()" />
        @elseif ($transaction->isVoteCombination())
            <x-general.identity :model="$transaction->voted()">
                <x-slot name="icon">
                    <x-transactions.icon :icon-type="$iconType" />
                </x-slot>

                <x-slot name="prefix">
                    <span class="pr-2 mr-2 font-semibold border-r text-theme-secondary-900 border-theme-secondary-300 dark:text-theme-secondary-200 dark:border-theme-secondary-800">
                        @lang('general.transaction.types.vote-combination')
                    </span>
                </x-slot>
            </x-general.identity>
        @elseif ($transaction->isVote())
            <x-general.identity :model="$transaction->voted()">
                <x-slot name="icon">
                    <x-transactions.icon :icon-type="$iconType" />
                </x-slot>

                <x-slot name="prefix">
                    <span class="pr-2 mr-2 font-semibold border-r text-theme-secondary-900 border-theme-secondary-300 dark:text-theme-secondary-200 dark:border-theme-secondary-800">
                        @lang('general.transaction.types.vote')
                    </span>
                </x-slot>
            </x-general.identity>
        @elseif ($transaction->isUnvote())
            <x-general.identity :model="$transaction->unvoted()">
                <x-slot name="icon">
                    <x-transactions.icon :icon-type="$iconType" />
                </x-slot>

                <x-slot name="prefix">
                    <span class="pr-2 mr-2 font-semibold border-r text-theme-secondary-900 border-theme-secondary-300 dark:text-theme-secondary-200 dark:border-theme-secondary-800">
                        @lang('general.transaction.types.unvote')
                    </span>
                </x-slot>
            </x-general.identity>
        @else
            <div class="flex flex-row-reverse items-center md:flex-row">
                <x-transactions.icon :icon-type="$iconType" />

                <div class="mr-4 font-semibold md:mr-0 md:ml-4 text-theme-secondary-900 dark:text-theme-secondary-200">
                    @lang('general.transaction.types.'.$transaction->typeName())

                    @if ($transaction->isMultiPayment())
                        <span class="ml-1 text-theme-secondary-600">{{ $transaction->recipientsCount() }}</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
