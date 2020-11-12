@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @section('breadcrumbs')
        <x-general.breadcrumbs :crumbs="[
            ['route' => 'home', 'label' => trans('menus.home')],
            ['label' => trans('menus.search_results')],
        ]" />
    @endsection

    @section('content')
        <livewire:search-page />
    @endsection

@endcomponent
