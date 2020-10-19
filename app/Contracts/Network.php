<?php

declare(strict_types=1);

namespace  App\Contracts;

interface Network
{
    public function name(): string;

    public function symbol(): string;

    public function knownWallets(): array;

    public function canBeExchanged(): bool;
}
