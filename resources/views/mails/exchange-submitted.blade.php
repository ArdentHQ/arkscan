@component('mail::message')
# Exchange Submitted

Name: {{ $data['name'] }}

Pairs: {{ $data['pairs'] }}

Website: {{ $data['website'] }}

{{ $data['message'] }}
@endcomponent
