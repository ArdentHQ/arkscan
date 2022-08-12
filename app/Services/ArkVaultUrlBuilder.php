<?php

declare(strict_types=1);

namespace App\Services;

use Ardenthq\UrlBuilder\UrlBuilder;
use Ardenthq\UrlBuilder\Enums\Networks;
use App\Facades\Network;

final class ArkVaultUrlBuilder
{
    public static function get(): UrlBuilder
    {
        $urlBuilder = new UrlBuilder(config('explorer.vault_url'));

        $urlBuilder->setNetwork(Network::alias() === 'mainnet' ? Networks::ARKMainnet : Networks::ARKDevnet);

        return $urlBuilder;
    }
}
