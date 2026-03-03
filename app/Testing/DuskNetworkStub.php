<?php

declare(strict_types=1);

namespace App\Testing;

use App\Contracts\Network as NetworkContract;
use App\DTO\Inertia\INetwork;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

final class DuskNetworkStub
{
    public const CAN_BE_EXCHANGED_CACHE_KEY = 'dusk.network.can_be_exchanged';

    public function __construct(private NetworkContract $network)
    {
        //
    }

    public function __call(string $method, array $arguments): mixed
    {
        return $this->network->{$method}(...$arguments);
    }

    public function canBeExchanged(): bool
    {
        $override = Cache::get(self::CAN_BE_EXCHANGED_CACHE_KEY);

        if (is_bool($override)) {
            return $override;
        }

        return $this->network->canBeExchanged();
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
            coin: $this->network->coin(),
            name: $this->network->name(),
            api: $this->network->api(),
            alias: $this->network->alias(),
            nethash: $this->network->nethash(),
            mainnetExplorerUrl: $this->network->mainnetExplorerUrl(),
            testnetExplorerUrl: $this->network->testnetExplorerUrl(),
            legacyExplorerUrl: $this->network->legacyExplorerUrl(),
            currency: $this->network->currency(),
            currencySymbol: $this->network->currencySymbol(),
            confirmations: $this->network->confirmations(),
            knownWallets: $this->network->knownWallets(),
            knownWalletsUrl: $this->network->knownWalletsUrl() ?? '',
            canBeExchanged: $this->canBeExchanged(),
            epoch: $this->network->epoch()->toIso8601String(),
            validatorCount: $this->network->validatorCount(),
            blockTime: $this->network->blockTime(),
            blockReward: $this->network->blockReward(),
            base58Prefix: $this->network->base58Prefix(),
            contractAddresses: $this->network->knownContracts(),
            contractMethods: $config['contract_methods'],
        );
    }
}
