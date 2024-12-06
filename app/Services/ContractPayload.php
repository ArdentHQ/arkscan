<?php

declare(strict_types=1);

namespace App\Services;

use ArkEcosystem\Crypto\Utils\AbiDecoder;

final class PayloadArgument
{
    private string $bytes;

    public function __construct(string $bytes)
    {
        $bytes = hex2bin($bytes);
        if ($bytes === false) {
            $bytes = '';
        }

        $this->bytes = $bytes;
    }

    public function decodeAddress(): string
    {
        [$value] = AbiDecoder::decodeAddress($this->bytes, 0);

        return $value;
    }

    public function decodeUnsignedInt(): string
    {
        [$value] = AbiDecoder::decodeNumber($this->bytes, 0, 32, false);

        return $value;
    }
}
