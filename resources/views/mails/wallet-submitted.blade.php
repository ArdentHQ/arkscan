@component('mail::message')
# Wallet Submitted

Name: {{ $data['name'] }}

Website: {{ $data['website'] }}

{{ $data['message'] }}
@endcomponent
