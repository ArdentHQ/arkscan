<?php

declare(strict_types=1);

namespace App\Services\Monitor;

use App\Facades\Network;
use App\Models\Block;
use Carbon\Carbon;

final class ForgingInfoCalculator
{
    public static function calculate(int $roundHeight, int $currentHeight): array
    {
        [$currentForger, $nextForger] = static::findIndex($roundHeight, $currentHeight);

        return [
            'currentForger' => $currentForger,
            'nextForger'    => $nextForger,
        ];
    }

    private static function findIndex(int $roundHeight, int $currentHeight): array
    {
        $activeValidators    = Network::validatorCount();

        $currentForger = ($currentHeight - $roundHeight) % $activeValidators;
        $nextForger    = ($currentForger + 1) % $activeValidators;

        return [$currentForger, $nextForger];
    }
}
