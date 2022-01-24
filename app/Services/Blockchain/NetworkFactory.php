<?php

declare(strict_types=1);

namespace App\Services\Blockchain;

use InvalidArgumentException;

final class NetworkFactory
{
    public static function make(string $name): Network
    {
        if (in_array($name, ['development', 'production'], true)) {
            return new Network(config("explorer.networks.$name"));
        }

        throw new InvalidArgumentException(trans('exceptions.invalid_network'));
    }
}
