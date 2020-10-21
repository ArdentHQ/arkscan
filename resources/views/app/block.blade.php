@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @push('metatags')
        <meta property="og:title" content="@lang('metatags.block.title')" />
        <meta property="og:description" content="@lang('metatags.block.description')">
    @endpush

    @section('breadcrumbs')
        <x-breadcrumbs :crumbs="[
            ['route' => 'home', 'label' => trans('menus.home')],
            ['label' => trans('menus.block')],
        ]" />
    @endsection

    @section('content')
        <x-block.header />
    @endsection

@endcomponent
