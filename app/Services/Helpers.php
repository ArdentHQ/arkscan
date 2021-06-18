<?php

declare(strict_types=1);

namespace App\Services;

final class Helpers
{
    /**
     * Blends multiple parameters and creates an unique 10 chars string.
     * Useful for wire:keys.
     */
    public static function generateHashId(int | bool | string | null ...$params): string
    {
        return substr(md5(implode('*', $params)), 0, 10);
    }
}
