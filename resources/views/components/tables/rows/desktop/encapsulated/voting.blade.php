@props([
    'model'
])

@if($model->isVoting())
    <div class="flex justify-center items-center w-full">
        <x-ark-icon name="check-mark-box" size="sm" />
    </div>
@endif
