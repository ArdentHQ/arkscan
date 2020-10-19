<?php

declare(strict_types=1);

namespace App\Services\Blockchain;

use App\Contracts\Network;
use App\Services\Blockchain\Networks\ARK\Development;
use App\Services\Blockchain\Networks\ARK\Production;
use InvalidArgumentException;

final class NetworkFactory
{
    public static function make(string $name): Network
    {
        if ($name === 'ark.production') {
            return new Production();
        }

        if ($name === 'ark.development') {
            return new Development();
        }

        throw new InvalidArgumentException(__('exceptions.invalid_network'));
    }
}
