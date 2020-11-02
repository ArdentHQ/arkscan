<?php

declare(strict_types=1);

namespace App\Services;

use App\Facades\Network;
use ArkEcosystem\Crypto\Identities\Address;

final class Identity
{
    public static function address(string $publicKey): string
    {
        return Address::fromPublicKey($publicKey, Network::config());
    }
}
