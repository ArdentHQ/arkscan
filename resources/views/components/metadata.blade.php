@props([
    'page'   => 'home',
    'detail' => [],
])

@php
    $detail = array_merge(['name' => Network::currency()], $detail);
@endphp

@section('title')
    @lang('metatags.'.$page.'.title', $detail)
@endsection

@section('meta-title')
    @lang("metatags.{$page}.title", $detail)
@endsection

@isset(trans('metatags.'.$page)['description'])
    @section('meta-description')
        @lang("metatags.{$page}.description", $detail)
    @endsection
@endisset

@isset(trans('metatags.'.$page)['image'])
    @section('meta-image')
        @lang("metatags.{$page}.image", $detail)
    @endsection
@endisset
