<div class="flex overflow-hidden flex-col rounded-lg border border-theme-secondary-300 dark:border-theme-secondary-800">
    <div class="p-8 bg-black dark:bg-theme-secondary-900" wire:poll.{{ Network::blockTime() }}s>
        <div class="grid grid-cols-1 grid-flow-row gap-6 gap-y-8 w-full md:grid-cols-2 xl:grid-cols-4 delegate-statistics-grid">
        <x-delegates.statistic>
            <x-slot name="logo">
                <x-ark-icon name="user-team" size="md" />
            </x-slot>

            <x-slot name="title">
                @lang("pages.delegates.statistics.delegate_registrations")
            </x-slot>

            <x-number>{{ $delegateRegistrations }}</x-number>
        </x-delegates.statistic>

        <x-delegates.statistic>
            <x-slot name="logo">
                <x-ark-icon name="app-reward" />
            </x-slot>

            <x-slot name="title">
                @lang("pages.delegates.statistics.block_reward")
            </x-slot>

            <x-currency :currency="Network::currency()">{{ $blockReward }}</x-currency>
        </x-delegates.statistic>

        <x-delegates.statistic>
            <x-slot name="logo">
                <x-ark-icon name="app-fee" />
            </x-slot>

            <x-slot name="title">
                @lang("pages.delegates.statistics.fees_collected")
            </x-slot>

            <x-currency :currency="Network::currency()">{{ $feesCollected }}</x-currency>
        </x-delegates.statistic>

        <x-delegates.statistic>
            <x-slot name="logo">
                <x-ark-icon name="app-votes" />
            </x-slot>

            <x-slot name="title">
                @lang("pages.delegates.statistics.votes")
            </x-slot>

            <x-currency :currency="Network::currency()">{{ $votes }}</x-currency>

            <span class="text-theme-secondary-600 dark:text-theme-secondary-700">
                <x-percentage>{{ $votesPercentage }}</x-percentage>
            </span>
        </x-delegates.statistic>
    </div>
</div>
