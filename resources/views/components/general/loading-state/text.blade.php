<div
    wire:loading.class.remove="hidden"
    class="text-transparent inline-block rounded-md hidden loading-state {{ $class ?? '' }}"
>
    {{ $text }}
</div>
