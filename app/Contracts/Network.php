<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Services\BigNumber;
use Carbon\Carbon;

interface Network
{
    public function coin(): string;

    public function name(): string;

    public function alias(): string;

    public function api(): string;

    public function explorerTitle(): string;

    public function currency(): string;

    public function currencySymbol(): string;

    public function confirmations(): int;

    public function knownWalletsUrl(): string;

    public function knownWallets(): array;

    public function canBeExchanged(): bool;

    public function epoch(): Carbon;

    public function validatorCount(): int;

    public function blockTime(): int;

    public function blockReward(): int;

    public function supply(): BigNumber;

    public function config(): \BitWasp\Bitcoin\Network\Network;

    public function toArray(): array;
}
