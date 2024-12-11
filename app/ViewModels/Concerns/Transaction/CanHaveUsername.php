<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use ArkEcosystem\Crypto\Utils\Abi\ArgumentDecoder;

trait CanHaveUsername
{
    public function username(): ?string
    {
        $methodArguments = $this->methodArguments();
        if (count($methodArguments) === 0) {
            return null;
        }

        return (new ArgumentDecoder(implode($methodArguments)))->decodeString();
    }
}
