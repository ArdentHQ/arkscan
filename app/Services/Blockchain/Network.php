<?php

declare(strict_types=1);

namespace App\Services\Blockchain;

use Carbon\Carbon;
use App\Models\State;
use App\Services\BigNumber;
use Illuminate\Support\Arr;
use App\DTO\Inertia\INetwork;
use App\Services\Cache\WalletCache;
use App\Contracts\Network as Contract;
use ArkEcosystem\Crypto\Networks\AbstractNetwork;
use Illuminate\Validation\Rules\In;

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

    public function legacyExplorerUrl(): string
    {
        return $this->config['legacyExplorerUrl'];
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

    public function knownWalletsUrl(): string
    {
        return $this->config['knownWallets'];
    }

    public function knownWallets(): array
    {
        if (is_null(Arr::get($this->config, 'knownWallets'))) {
            return [];
        }

        return (new WalletCache())->getKnown();
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

    public function base58Prefix(): int
    {
        return $this->config['base58Prefix'];
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

    public function data(): INetwork
    {
        return new INetwork(
            coin: $this->coin(),
            name: $this->name(),
            api: $this->api(),
            alias: $this->alias(),
            nethash: $this->nethash(),
            mainnetExplorerUrl: $this->mainnetExplorerUrl(),
            testnetExplorerUrl: $this->testnetExplorerUrl(),
            legacyExplorerUrl: $this->legacyExplorerUrl(),
            currency: $this->currency(),
            currencySymbol: $this->currencySymbol(),
            confirmations: $this->confirmations(),
            knownWallets: $this->knownWallets(),
            knownWalletsUrl: $this->knownWalletsUrl(),
            canBeExchanged: $this->canBeExchanged(),
            epoch: $this->epoch()->toIso8601String(),
            validatorCount: $this->validatorCount(),
            blockTime: $this->blockTime(),
            blockReward: $this->blockReward(),
            base58Prefix: $this->base58Prefix(),
            contractAddresses: $this->knownContracts(),
            contractMethods: $this->config['contract_methods'],
        );
    }
}
