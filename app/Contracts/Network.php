<?php

declare(strict_types=1);

namespace App\Contracts;

use Carbon\Carbon;

interface Network
{
    public function name(): string;

    public function alias(): string;

    public function api(): string;

    public function explorerTitle(): string;

    public function currency(): string;

    public function currencySymbol(): string;

    public function confirmations(): int;

    public function knownWallets(): array;

    public function canBeExchanged(): bool;

    public function hasTimelock(): bool;

    public function epoch(): Carbon;

    public function delegateCount(): int;

    public function blockTime(): int;

    public function blockReward(): int;

    public function config(): \BitWasp\Bitcoin\Network\Network;
}
