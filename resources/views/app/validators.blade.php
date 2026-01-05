@component('layouts.app')
    <x-metadata page="validators" />

    @section('content')
        <livewire:validators.header-stats />

        <div class="px-6 pb-8 md:px-10 md:pb-6 md:mx-auto md:max-w-7xl">
            <livewire:validators.tabs />
        </div>
    @endsection

    @push('scripts')
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                hideTableTooltipsOnLivewireEvent(/^validators\./);
            });
        </script>
    @endpush
@endcomponent
