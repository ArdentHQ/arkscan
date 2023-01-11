<?php

declare(strict_types=1);

namespace App\Services;

final class Polygonscan
{
    public static function url(string $address) : string
    {
        return sprintf(
            '%s/address/%s',
            rtrim(config('explorer.polygonscan_url'), '/'),
            $address
        );
    }
}
