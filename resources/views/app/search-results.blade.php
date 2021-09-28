@component('layouts.app', ['isLanding' => true, 'fullWidth' => true])
    <x-metadata page="search" :detail="['searchTerm' => request()->get('term')]" />

    @section('content')
        <livewire:search-page />
    @endsection
@endcomponent
