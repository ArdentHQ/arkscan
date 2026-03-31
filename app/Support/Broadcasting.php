<?php

declare(strict_types=1);

namespace App\Support;

final class Broadcasting
{
    public static function usesWebSockets(): bool
    {
        return config('broadcasting.default') === 'reverb';
    }
}
