<?php

declare(strict_types=1);

namespace App\Services\Blockchain;

use App\Contracts\Network as Contract;
use App\Services\Cache\WalletCache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

final class Network implements Contract
{
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function name(): string
    {
        return $this->config['name'];
    }

    public function alias(): string
    {
        return $this->config['alias'];
    }

    public function currency(): string
    {
        return $this->config['currency'];
    }

    public function currencySymbol(): string
    {
        return $this->config['currencySymbol'];
    }

    public function confirmations(): int
    {
        return $this->config['confirmations'];
    }

    public function knownWallets(): array
    {
        return (new WalletCache())->setKnown(fn () => Http::get($this->config['knownWallets'])->json());
    }

    public function canBeExchanged(): bool
    {
        return $this->config['canBeExchanged'];
    }

    public function usesMarketsquare(): bool
    {
        return $this->config['usesMarketsquare'];
    }

    public function epoch(): Carbon
    {
        return Carbon::parse($this->config['epoch']);
    }

    public function delegateCount(): int
    {
        return $this->config['delegateCount'];
    }

    public function blockTime(): int
    {
        return $this->config['blockTime'];
    }

    public function blockReward(): int
    {
        return $this->config['blockReward'];
    }

    public function config(): \BitWasp\Bitcoin\Network\Network
    {
        return resolve($this->config['config']);
    }
}
