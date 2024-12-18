@props([
    'title',
    'valueTitle',
    'model',
    'value'
])

<div class="hidden justify-between w-full md:flex xl:w-[770px]">
    <div class="flex flex-1">
        {{ $title }}
    </div>
    <div class="flex flex-col flex-1 justify-between space-y-3 md-lg:flex-2 md-lg:flex-row md-lg:space-y-0">
        <div class="flex flex-1 justify-end text-theme-secondary-900 dark:text-theme-dark-50">
            @if ($model === null)
                <div class="dark:text-theme-dark-200">
                    @lang('general.na')
                </div>
            @else
                <a
                    href="{{ route('wallet', $model->model()) }}"
                    class="link"
                >
                    @if ($model->hasUsername())
                        {{ $model->usernameBeforeKnown() }}
                    @else
                        {{ $model->address() }}
                    @endif
                </a>
            @endif
        </div>

        <div class="flex flex-1 justify-between space-x-2 w-full md-lg:pl-16">
            <div>
                {{ $valueTitle }}:
            </div>
            <div class="text-theme-secondary-900 dark:text-theme-dark-50">
                @if(is_numeric($value))
                    <x-number>{{ $value }}</x-number>
                @else
                    {{ $value }}
                @endif
            </div>
        </div>
    </div>
</div>
