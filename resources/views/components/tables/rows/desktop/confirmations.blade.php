<td class="hidden text-right xl:table-cell">
    <div wire:loading.class="w-full h-4 rounded-md bg-theme-secondary-300 animate-pulse"></div>

    @if($model->isConfirmed())
        <span wire:loading.class="hidden">@lang('general.confirmed')</span>
    @else
        <span wire:loading.class="hidden">{{ $model->confirmations() }}/{{ Network::confirmations() }}</span>
    @endif
</td>
