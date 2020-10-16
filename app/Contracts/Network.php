<?php

declare(strict_types=1);

namespace  App\Contracts;

interface Network
{
    public function knownWallets(): array;
}
