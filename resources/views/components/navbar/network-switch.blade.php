<div {{ $attributes->class('flex items-center space-x-3') }}">
    <x-navbar.network-select
        :url="config('explorer.networks.production.explorerUrl')"
        :label="trans('menus.network-select.main.label')"
        :mobile-label="trans('menus.network-select.main.mobile-label')"
        :active="Network::alias() === 'mainnet'"
    />

    <x-navbar.network-select
        :url="config('explorer.networks.development.explorerUrl')"
        :label="trans('menus.network-select.test.label')"
        :mobile-label="trans('menus.network-select.test.mobile-label')"
        :active="Network::alias() !== 'mainnet'"
    />
</div>
