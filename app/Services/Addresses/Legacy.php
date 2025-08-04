<?php

declare(strict_types=1);

namespace App\Services\Addresses;

use App\Facades\Network;
use ArkEcosystem\Crypto\Binary\UnsignedInteger\Writer;
use ArkEcosystem\Crypto\Identities\PublicKey;
use BitWasp\Bitcoin\Base58;
use BitWasp\Bitcoin\Crypto\Hash;
use BitWasp\Buffertools\Buffer;

final class Legacy
{
    public static function generateAddressFromPublicKey(string $publicKey): string
    {
        $base58Prefix = Network::base58Prefix();

        $ripemd160 = Hash::ripemd160(PublicKey::fromHex($publicKey)->instance->getBuffer());
        $seed      = Writer::bit8($base58Prefix).$ripemd160->getBinary();

        return Base58::encodeCheck(new Buffer($seed));
    }
}
