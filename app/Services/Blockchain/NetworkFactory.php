<?php

namespace App\Services\Blockchain;

use App\Contracts\Network;
use App\Services\Blockchain\Networks\ARK\Development;
use App\Services\Blockchain\Networks\ARK\Production;
use InvalidArgumentException;

class NetworkFactory
{
    public static function make(string $name): Network
    {
        switch ($name) {
            case 'ark.production':
                return new Production();
            case 'ark.development':
                return new Development();
            default:
                throw new InvalidArgumentException(__('exceptions.invalid_network'));
        }
    }
}
