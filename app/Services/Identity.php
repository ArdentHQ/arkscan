<?php

declare(strict_types=1);

namespace App\Services;

use ArkEcosystem\Crypto\Identities\Address;
use Illuminate\Support\Facades\Cache;

final class Identity
{
    public static function address(string $publicKey): string
    {
        return Cache::tags('identity')->remember(
            $publicKey,
            now()->addMinutes(10),
            fn () => Address::fromPublicKey($publicKey)
        );
    }
}
