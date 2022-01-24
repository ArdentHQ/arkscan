<div class="avatar-wrapper overflow-hidden rounded-full flex-shrink-0 @if(isset($noShrink)) w-11 h-11 @else w-6 h-6 md:w-11 md:h-11 @endif">
    <div class="object-cover overflow-hidden w-full h-full rounded-full">
        {!! Avatar::make($identifier) !!}
    </div>
</div>
