@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @section('content')
        <x-general.search.header />

        <x-ark-container>
            <div x-cloak class="w-full">
                <div class="flex relative justify-between items-center">
                    <h2>@lang('pages.blocks.title')</h2>
                </div>

                <livewire:block-table />
            </div>
        </x-ark-container>
    @endsection

@endcomponent
