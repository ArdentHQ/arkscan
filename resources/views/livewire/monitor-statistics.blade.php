<div class="flex flex-col overflow-hidden border rounded-lg border-theme-secondary-300 dark:border-theme-secondary-800">
    <div class="p-8 bg-black dark:bg-theme-secondary-900" wire:poll.8s>
        <div class="grid w-full grid-flow-row grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4 gap-y-12 xl:gap-y-4">
        <x-monitor.statistic>
            <x-slot name="logo">
                @svg('app-fee', 'w-5 h-5')
            </x-slot>

            <x-slot name="title">
                @lang("pages.monitor.statistics.delegate_registrations")
            </x-slot>

            {{ $delegateRegistrations }}
        </x-monitor.statistic>

        <x-monitor.statistic>
            <x-slot name="logo">
                @svg('app-reward', 'w-5 h-5')
            </x-slot>

            <x-slot name="title">
                @lang("pages.monitor.statistics.block_reward")
            </x-slot>

            {{ $blockReward }}
        </x-monitor.statistic>

        <x-monitor.statistic>
            <x-slot name="logo">
                @svg('app-fee', 'w-5 h-5')
            </x-slot>

            <x-slot name="title">
                @lang("pages.monitor.statistics.fees_collected")
            </x-slot>

            {{ $feesCollected }}
        </x-monitor.statistic>

        <x-monitor.statistic>
            <x-slot name="logo">
                @svg('app-votes', 'w-5 h-5')
            </x-slot>

            <x-slot name="title">
                @lang("pages.monitor.statistics.votes")
            </x-slot>

            {{ $votes }} <span class="text-theme-secondary-600 dark:text-theme-secondary-700">{{ $votesPercentage }}</span>
        </x-monitor.statistic>
    </div>
</div>
