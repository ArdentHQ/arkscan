@props([
    'navigation' => null,
])

@php
    if (is_null($navigation)) {
        $navigation = [
            ['route' => 'home', 'label' => trans('menus.home')],
            ['label' => trans('menus.blockchain'), 'children' => [
                ['route' => 'blocks',           'label' => trans('menus.blocks')],
                ['route' => 'transactions',     'label' => trans('menus.transactions')],
                ['route' => 'validators',        'label' => trans('menus.validators')],
                ['route' => 'top-accounts',     'label' => trans('menus.top_accounts')],
                ['route' => 'statistics',       'label' => trans('menus.statistics')],
            ]],
            ['label' => trans('menus.resources'), 'children' => [
                ['route' => 'validator-monitor', 'label' => trans('menus.validator_monitor')],
                ['route' => 'compatible-wallets',  'label' => trans('menus.wallets')],
            ]],
            ['label' => trans('menus.developers'), 'children' => [
                ['url' => trans('urls.docs.arkscan'),  'label' => trans('menus.docs')],
                ['url' => trans('urls.docs.api'),  'label' => trans('menus.api')],
                ['url' => trans('urls.github'),  'label' => trans('menus.github')],
            ]],
        ];

        if (Network::canBeExchanged()) {
            $navigation[2]['children'][] = ['route' => 'exchanges',  'label' => trans('menus.exchanges')];
        }

        if (config('arkscan.support.enabled') === true) {
            $navigation[3]['children'][] = ['route' => 'contact', 'label' => trans('menus.support')];
        }
    }
@endphp

<div
    id="navbar"
    class="z-30 sm:pb-16 md:sticky md:top-0 md:pb-0 pb-13"
>
    <x-navbar.top />
    <x-navbar.desktop :navigation="$navigation" />
    <x-navbar.mobile :navigation="$navigation" />
</div>
