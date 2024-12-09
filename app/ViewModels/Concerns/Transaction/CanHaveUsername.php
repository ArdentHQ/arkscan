<?php

declare(strict_types=1);

namespace App\ViewModels\Concerns\Transaction;

use ArkEcosystem\Crypto\Utils\Abi\ArgumentDecoder;

trait CanHaveUsername
{
    /**
     * Get the username.
     *
     * @return string|null
     */
    public function username()
    {
        $methodArguments = $this->methodArguments();
        if (count($methodArguments) === 0) {
            return;
        }

        return (new ArgumentDecoder(implode($methodArguments)))->decodeString();
    }
}
