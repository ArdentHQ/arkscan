<?php

declare(strict_types=1);

namespace App\Services\Blockchain;

use App\Contracts\Network as Contract;
use App\Models\State;
use App\Services\BigNumber;
use App\Services\Cache\WalletCache;
use ArkEcosystem\Crypto\Networks\AbstractNetwork;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

final class Network implements Contract
{
    public function __construct(private array $config)
    {
    }

    public function coin(): string
    {
        return $this->config['coin'];
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
        return config('app.name');
    }

    public function mainnetExplorerUrl(): string
    {
        return $this->config['mainnetExplorerUrl'];
    }

    public function testnetExplorerUrl(): string
    {
        return $this->config['testnetExplorerUrl'];
    }

    public function currency(): string
    {
        return $this->config['currency'];
    }

    public function currencySymbol(): string
    {
        return $this->config['currencySymbol'];
    }

    public function nethash(): string
    {
        return $this->config['nethash'];
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

    public function knownContracts(): array
    {
        return $this->config['contract_addresses'];
    }

    public function knownContract(string $name): ?string
    {
        return Arr::get($this->knownContracts(), $name);
    }

    public function contractMethod(string $name, string $default): string
    {
        $method = Arr::get($this->config['contract_methods'], $name);
        if ($method === null) {
            return $default;
        }

        return $method;
    }

    public function canBeExchanged(): bool
    {
        return $this->config['canBeExchanged'];
    }

    public function epoch(): Carbon
    {
        return Carbon::parse($this->config['epoch']);
    }

    public function validatorCount(): int
    {
        return $this->config['validatorCount'];
    }

    public function blockTime(): int
    {
        return $this->config['blockTime'];
    }

    public function blockReward(): int
    {
        return $this->config['blockReward'];
    }

    public function supply(): BigNumber
    {
        $latestState = State::first();
        if ($latestState === null) {
            return BigNumber::zero();
        }

        return $latestState->supply;
    }

    public function config(): AbstractNetwork
    {
        return new CustomNetwork($this->config);
    }

    public function toArray(): array
    {
        return $this->config;
    }
}
