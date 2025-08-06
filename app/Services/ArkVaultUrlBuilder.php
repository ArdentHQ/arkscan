<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\Network;
use Ardenthq\UrlBuilder\UrlBuilder;

final class ArkVaultUrlBuilder
{
    public static function get(): UrlBuilder
    {
        return (new UrlBuilder(config('arkscan.urls.vault_url')))
            ->setNetwork(Network::nethash())
            ->setCoin(Network::coin());
    }
}
