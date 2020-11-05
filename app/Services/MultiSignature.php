<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\Network;

final class MultiSignature
{
    public static function address(int $min, array $publicKeys): string
    {
        return trim(shell_exec(sprintf(
            "%s %s %s '%s'",
            config('explorer.nodejs'),
            base_path('musig.js'),
            Network::alias(),
            json_encode(['min' => $min, 'publicKeys' => $publicKeys])
        )) ?? '');
    }
}
