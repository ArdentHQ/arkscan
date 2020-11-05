@php
    $size = [
        'xs'   => 'w-3 h-3 ',
        'sm'   => 'w-4 h-4 ',
        'md'   => 'w-6 h-6 ',
        'lg'   => 'w-8 h-8 ',
        'xl'   => 'w-11 h-11 ',
        'xxl'  => 'w-12 h-12 ',
        'base' => 'w-5 h-5 ',
    ][$size ?? 'base'];

    if (isset($style)) {
        $style = [
            'secondary' => 'text-theme-secondary-500 ',
            'success'   => 'text-theme-success-500 ',
            'danger'    => 'text-theme-danger-500 ',
        ][$style];
    }
@endphp

@svg($name, $size . ($style ?? '') . ($class ?? ''))
