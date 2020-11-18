@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @section('breadcrumbs')
        <x-ark-breadcrumbs :crumbs="[
            ['route' => 'home', 'label' => trans('menus.home')],
            ['label' => trans('menus.wallets')],
        ]" />
    @endsection

    @section('content')
        <x-general.search.header />

        <x-wallets.sorted-by-balance />
    @endsection

@endcomponent
