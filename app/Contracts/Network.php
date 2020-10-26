<?php

declare(strict_types=1);

namespace App\Contracts;

use Carbon\Carbon;

interface Network
{
    public function name(): string;

    public function alias(): string;

    public function currency(): string;

    public function currencySymbol(): string;

    public function confirmations(): int;

    public function knownWallets(): array;

    public function canBeExchanged(): bool;

    public function host(): string;

    public function usesMarketsquare(): bool;

    public function epoch(): Carbon;

    public function delegateCount(): int;

    public function blockTime(): int;

    public function config(): \BitWasp\Bitcoin\Network\Network;
}
