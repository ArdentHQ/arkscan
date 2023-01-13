<?php

declare(strict_types=1);

namespace App\Services\Blockchain;

use App\Contracts\Network as Contract;
use App\Models\Wallet;
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

    public function polygonExplorerUrl(): string
    {
        return $this->config['polygonExplorerUrl'];
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

    public function canBeExchanged(): bool
    {
        return $this->config['canBeExchanged'];
    }

    public function hasTimelock(): bool
    {
        return $this->config['hasTimelock'];
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

    public function supply(): BigNumber
    {
        return BigNumber::new(Wallet::where('balance', '>', 0)->sum('balance'))
            ->minus($this->migratedBalance()->valueOf());
    }

    public function migratedBalance(): BigNumber
    {
        $wallet = Wallet::firstWhere('address', config('explorer.migration_address'));
        if ($wallet === null) {
            return BigNumber::new(0);
        }

        return $wallet->balance;
    }

    public function config(): AbstractNetwork
    {
        return new CustomNetwork($this->config);
    }

    public function hasMigration(): bool
    {
        return config('explorer.migration_address') !== null && config('explorer.migration_address') !== '';
    }
}
