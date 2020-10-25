@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @push('metatags')
        <meta property="og:title" content="@lang('metatags.block.title')" />
        <meta property="og:description" content="@lang('metatags.block.description')">
    @endpush

    @push('scripts')
        <script src="{{ mix('js/clipboard.js')}}"></script>
    @endpush

    @section('breadcrumbs')
        <x-general.breadcrumbs :crumbs="[
            ['route' => 'home', 'label' => trans('menus.home')],
            ['label' => trans('menus.block')],
        ]" />
    @endsection

    @section('content')
        <x-block.header :block="$block" />

        <x-details.grid>
            <x-details.generic
                :title="trans('general.block.height')"
                :value="$block->height()"
                icon="app-volume" />

            <x-details.generic
                :title="trans('general.block.timestamp')"
                :value="$block->timestamp()"
                icon="app-volume" />

            <x-details.generic
                :title="trans('general.block.reward')"
                :value="$block->reward()"
                icon="app-volume" />

            <x-details.generic
                :title="trans('general.block.fee')"
                :value="$block->fee()"
                icon="app-volume" />

            <x-details.generic
                :title="trans('general.block.confirmations')"
                :value="$block->confirmations()"
                icon="app-volume" />
        </x-details.grid>
    @endsection

@endcomponent
