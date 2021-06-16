<?php

declare(strict_types=1);

namespace App\Services\Blockchain;

use App\Contracts\Network as Contract;
use App\Services\Cache\WalletCache;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

final class Network implements Contract
{
    public function __construct(private array $config)
    {
    }

    public function name(): string
    {
        return $this->config['name'];
    }

    public function alias(): string
    {
        return $this->config['alias'];
    }

    public function api(): string
    {
        return $this->config['api'];
    }

    public function explorerTitle(): string
    {
        return $this->currency().' '.trans('general.explorer');
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
        if (is_null(Arr::get($this->config, 'knownWallets'))) {
            return [];
        }

        return (new WalletCache())->setKnown(fn () => Http::get($this->config['knownWallets'])->json());
    }

    public function canBeExchanged(): bool
    {
        return $this->config['canBeExchanged'];
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
        return new CustomNetwork($this->config);
    }
}
