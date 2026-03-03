<?php

declare(strict_types=1);

namespace App\Testing;

use App\Contracts\Network as NetworkContract;
use App\DTO\Inertia\INetwork;
use App\Services\BigNumber;
use BitWasp\Bitcoin\Network\Network as BitcoinNetwork;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

final class DuskNetworkStub implements NetworkContract
{
    public const CAN_BE_EXCHANGED_CACHE_KEY = 'dusk.network.can_be_exchanged';

    public function __construct(private NetworkContract $network)
    {
    }

    public function canBeExchanged(): bool
    {
        $override = Cache::get(self::CAN_BE_EXCHANGED_CACHE_KEY);

        if (is_bool($override)) {
            return $override;
        }

        return $this->network->canBeExchanged();
    }

    public function coin(): string
    {
        return $this->network->coin();
    }

    public function name(): string
    {
        return $this->network->name();
    }

    public function alias(): string
    {
        return $this->network->alias();
    }

    public function api(): string
    {
        return $this->network->api();
    }

    public function explorerTitle(): string
    {
        return $this->network->explorerTitle();
    }

    public function currency(): string
    {
        return $this->network->currency();
    }

    public function currencySymbol(): string
    {
        return $this->network->currencySymbol();
    }

    public function confirmations(): int
    {
        return $this->network->confirmations();
    }

    public function knownWalletsUrl(): ?string
    {
        return $this->network->knownWalletsUrl();
    }

    public function knownWallets(): array
    {
        return $this->network->knownWallets();
    }

    public function whitelistedTokensUrl(): ?string
    {
        return $this->network->whitelistedTokensUrl();
    }

    public function knownContracts(): array
    {
        return $this->network->knownContracts();
    }

    public function knownContract(string $name): ?string
    {
        return $this->network->knownContract($name);
    }

    public function contractMethod(string $name, string $default): ?string
    {
        return $this->network->contractMethod($name, $default);
    }

    public function epoch(): Carbon
    {
        return $this->network->epoch();
    }

    public function validatorCount(): int
    {
        return $this->network->validatorCount();
    }

    public function blockTime(): int
    {
        return $this->network->blockTime();
    }

    public function blockReward(): int
    {
        return $this->network->blockReward();
    }

    public function supply(): BigNumber
    {
        return $this->network->supply();
    }

    public function config(): BitcoinNetwork
    {
        return $this->network->config();
    }

    public function toArray(): array
    {
        $config                   = $this->network->toArray();
        $config['canBeExchanged'] = $this->canBeExchanged();

        return $config;
    }

    public function data(): INetwork
    {
        $config = $this->toArray();

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
            knownWalletsUrl: $this->knownWalletsUrl() ?? '',
            canBeExchanged: $this->canBeExchanged(),
            epoch: $this->epoch()->toIso8601String(),
            validatorCount: $this->validatorCount(),
            blockTime: $this->blockTime(),
            blockReward: $this->blockReward(),
            base58Prefix: $this->network->base58Prefix(),
            contractAddresses: $this->knownContracts(),
            contractMethods: $config['contract_methods'],
        );
    }

    public function nethash(): string
    {
        return $this->network->nethash();
    }

    public function mainnetExplorerUrl(): string
    {
        return $this->network->mainnetExplorerUrl();
    }

    public function testnetExplorerUrl(): string
    {
        return $this->network->testnetExplorerUrl();
    }

    public function legacyExplorerUrl(): string
    {
        return $this->network->legacyExplorerUrl();
    }

    public function base58Prefix(): int
    {
        return $this->network->base58Prefix();
    }
}
