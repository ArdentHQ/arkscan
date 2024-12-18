@props([
    'title',
    'valueTitle',
    'model',
    'value'
])

<div class="flex md:hidden">
    <div class="flex flex-col justify-between pt-3 space-y-3 w-full sm:flex-row sm:space-y-0">
        <div class="flex flex-col space-y-2">
            <span>{{ $title }}</span>
            <span class="text-theme-secondary-900 dark:text-theme-dark-50">
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
            </span>
        </div>

        <div class="flex flex-col space-y-2 w-[90px]">
            <div>
                {{ $valueTitle }}
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
