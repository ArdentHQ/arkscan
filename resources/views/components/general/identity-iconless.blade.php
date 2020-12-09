<div class="flex justify-between items-center space-x-3">
    <div class="flex items-center max-w-full">
        @if ($prefix ?? false)
            {{ $prefix }}
        @endif

        <a href="{{ route('wallet', $model->address()) }}" class="max-w-full font-semibold link">
            @if ($model->username())
                {{ $model->username() }}
            @else
                @isset($withoutTruncate)
                    {{ $model->address() }}
                @else
                    @isset($dynamicTruncate)
                        <x-truncate-dynamic>{{ $model->address() }}</x-truncate-dynamic>
                    @else
                      <x-truncate-middle length="{{ $length ?? 8 }}">
                          {{ $model->address() }}
                      </x-truncate-middle>
                    @endif
                @endisset
            @endif
        </a>
    </div>
</div>
