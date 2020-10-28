<div>
    @lang('labels.slot_time')

    <div wire:loading.class="w-full h-5 rounded-full bg-theme-secondary-300 animate-pulse"></div>
    <div wire:loading.class="hidden">{{ $model->forgingAt() }}</div>
</div>
