<div class="navbar-settings-option @if($breakpoint ?? false) hidden {{ $breakpoint }}:flex @else flex @endif">
    <div>
        <div class="font-semibold dark:text-theme-secondary-200">{{ $title }}</div>
        <div class="mt-2 text-sm text-theme-secondary-500">{{ $subtitle }}</div>
    </div>

    <div>
        {{ $slot }}
    </div>
</div>
