@props([
    'model'
])

<a href="{{ $model->voteUrl() }}" target="_blank" class="button-vote">
    <span class="sm:hidden"><x-ark-icon name="app-transactions.vote" size="sm" /></span>
    <span class="hidden sm:inline">Vote</span>
</a>
