@component('layouts.app')
    <x-metadata page="search" :detail="['searchTerm' => request()->get('term')]" />

    @section('content')
        <livewire:search-page />
    @endsection
@endcomponent
