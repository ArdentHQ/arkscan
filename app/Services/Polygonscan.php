<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\Network;

final class Polygonscan
{
    public static function url(string $address) : string
    {
        return sprintf(
            '%s/address/%s',
            rtrim(Network::polygonExplorerUrl(), '/'),
            $address
        );
    }
}
