@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])

    @section('content')
        <x-ark-container>
            <div x-cloak class="w-full">
                <div class="flex relative justify-between items-center">
                    <h1 class="mb-3">@lang('pages.blocks.title')</h1>
                </div>

                <livewire:block-table />
            </div>
        </x-ark-container>
    @endsection

@endcomponent
