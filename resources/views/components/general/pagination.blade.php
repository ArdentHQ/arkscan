@if($results->hasPages())
    <div class="flex justify-center {{ $class ?? '' }}">
        {{ $results->links('vendor.ark.pagination') }}
    </div>
@endif
