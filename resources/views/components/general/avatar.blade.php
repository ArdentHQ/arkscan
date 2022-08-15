<div class="avatar-wrapper overflow-hidden rounded-full flex-shrink-0 @if(isset($noShrink)) w-11 h-11 @else w-5 h-5 lg:w-11 lg:h-11 @endif">
    <div class="object-cover overflow-hidden w-full h-full rounded-full">
        {!! Avatar::make($identifier) !!}
    </div>
</div>
