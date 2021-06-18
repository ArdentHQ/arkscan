<?php

declare(strict_types=1);

namespace App\Services;

final class Helpers
{
    /**
     * Blends multiple parameters into a single string.
     * Useful for wire:keys.
     */
    public static function generateId(int | bool | string | null ...$params): string
    {
        return implode('*', $params);
    }
}
