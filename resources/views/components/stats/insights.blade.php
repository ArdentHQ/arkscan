<div x-data="{ tab: 'transactions' }">
    <x-general.mobile-divider />

    <x-page-headers.generic
        :title="trans('pages.statistics.insights.title')"
        :subtitle="trans('pages.statistics.insights.subtitle')"
        class="!pb-4 !pt-6 md:!pt-0"
    />

    <x-stats.mobile-dropdown />

    <livewire:stats.insights />
</div>
