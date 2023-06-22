@props(['transaction'])

@php ($isVote = $transaction->isVote())
@php ($delegate = $isVote ? $transaction->voted() : $transaction->unvoted())

<div class="flex items-center space-x-2 text-xs">
    <div class="text-theme-secondary-500 dark:text-theme-secondary-700">
        @if ($isVote)
            @lang('general.search.vote')
        @else
            @lang('general.search.unvote')
        @endif
    </div>

    <x-general.identity
        :model="$delegate"
        without-reverse
        without-reverse-class="space-x-2"
        without-link
        without-icon
        class="text-theme-secondary-700 dark:text-theme-secondary-500"
    />
</div>
