<div {{ $attributes->class('flex items-center space-x-3') }}">
    <x-navbar.network-select
        :url="trans('menus.network-select.main.url')"
        :label="trans('menus.network-select.main.label')"
        :mobile-label="trans('menus.network-select.main.mobile-label')"
        :active="Network::alias() === 'mainnet'"
    />

    <x-navbar.network-select
        :url="trans('menus.network-select.test.url')"
        :label="trans('menus.network-select.test.label')"
        :mobile-label="trans('menus.network-select.test.mobile-label')"
        :active="Network::alias() !== 'mainnet'"
    />
</div>
