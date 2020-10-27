<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Contracts\ViewModel;
use App\Facades\Network;
use App\Models\Scopes\EntityRegistrationScope;
use App\Models\Wallet;
use App\Services\BigNumber;
use App\Services\Blockchain\NetworkStatus;
use App\Services\ExchangeRate;
use App\Services\NumberFormatter;
use App\Services\Timestamp;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Mattiasgeniar\Percentage\Percentage;

final class WalletViewModel implements ViewModel
{
    private Wallet $wallet;

    public function __construct(Wallet $wallet)
    {
        $this->wallet = $wallet;
    }

    public function url(): string
    {
        return route('wallet', $this->wallet->address);
    }

    public function address(): string
    {
        return $this->wallet->address;
    }

    public function publicKey(): ?string
    {
        return $this->wallet->public_key;
    }

    public function username(): string
    {
        return Arr::get($this->wallet, 'attributes.delegate.username');
    }

    /**
     * @codeCoverageIgnore
     */
    public function rank(): ?int
    {
        return Arr::get($this->wallet, 'attributes.delegate.rank');
    }

    public function balance(): string
    {
        return NumberFormatter::currency($this->wallet->balance->toFloat(), Network::currency());
    }

    public function balanceFiat(): string
    {
        return ExchangeRate::convert($this->wallet->balance->toFloat(), Timestamp::now()->unix());
    }

    public function balancePercentage(): string
    {
        return NumberFormatter::percentage(Percentage::calculate($this->wallet->balance->toFloat(), NetworkStatus::supply()));
    }

    public function nonce(): string
    {
        return NumberFormatter::number($this->wallet->nonce->toNumber());
    }

    public function votes(): string
    {
        return NumberFormatter::currency(
            BigNumber::new($this->wallet->attributes['delegate']['voteBalance'])->toFloat(),
            Network::currency()
        );
    }

    public function votesPercentage(): string
    {
        $voteBalance = (float) $this->wallet->attributes['delegate']['voteBalance'];

        return NumberFormatter::percentage(BigNumber::new(Percentage::calculate($voteBalance, NetworkStatus::supply()))->toFloat());
    }

    public function amountForged(): string
    {
        $result = Arr::get(Cache::get('delegates.totalAmounts', []), $this->wallet->public_key, 0);

        return NumberFormatter::currency(BigNumber::new($result)->toFloat(), Network::currency());
    }

    public function feesForged(): string
    {
        $result = Arr::get(Cache::get('delegates.totalFees', []), $this->wallet->public_key, 0);

        return NumberFormatter::currency(BigNumber::new($result)->toFloat(), Network::currency());
    }

    public function rewardsForged(): string
    {
        $result = Arr::get(Cache::get('delegates.totalRewards', []), $this->wallet->public_key, 0);

        return NumberFormatter::currency(BigNumber::new($result)->toFloat(), Network::currency());
    }

    public function isKnown(): bool
    {
        return ! is_null($this->findWalletByKnown());
    }

    public function isOwnedByTeam(): bool
    {
        if (! $this->isKnown()) {
            return false;
        }

        return optional($this->findWalletByKnown())['type'] === 'team';
    }

    public function isOwnedByExchange(): bool
    {
        if (! $this->isKnown()) {
            return false;
        }

        return optional($this->findWalletByKnown())['type'] === 'exchange';
    }

    /**
     * @TODO: needs marketsquare
     *
     * @codeCoverageIgnore
     */
    public function commission(): string
    {
        return NumberFormatter::percentage(0);
    }

    /**
     * @TODO: needs marketsquare
     *
     * @codeCoverageIgnore
     */
    public function payoutFrequency(): string
    {
        return NumberFormatter::number(0);
    }

    /**
     * @TODO: needs marketsquare
     *
     * @codeCoverageIgnore
     */
    public function payoutMinimum(): string
    {
        return NumberFormatter::number(0);
    }

    public function forgedBlocks(): string
    {
        return NumberFormatter::number(Arr::get($this->wallet, 'attributes.delegate.producedBlocks', 0));
    }

    /**
     * @TODO: needs monitor to be implemented
     */
    public function productivity(): string
    {
        return NumberFormatter::percentage(0);
    }

    public function isDelegate(): bool
    {
        return Arr::has($this->wallet, 'attributes.delegate');
    }

    public function hasRegistrations(): bool
    {
        return $this->wallet->sentTransactions()->withScope(EntityRegistrationScope::class)->count() > 0;
    }

    public function registrations(): Collection
    {
        return ViewModelFactory::collection($this->wallet->sentTransactions()->withScope(EntityRegistrationScope::class)->get());
    }

    public function isVoting(): bool
    {
        return ! is_null(Arr::get($this->wallet, 'attributes.vote'));
    }

    public function vote(): ?self
    {
        if (! Arr::has($this->wallet, 'attributes.vote')) {
            return null;
        }

        $wallet = Cache::get('votes.'.Arr::get($this->wallet, 'attributes.vote'));

        if (is_null($wallet)) {
            return null;
        }

        return new static($wallet);
    }

    public function isMissing(): bool
    {
        return false;
    }

    public function hasMissedRecently(): bool
    {
        return false;
    }

    private function findWalletByKnown(): ?array
    {
        return collect(Network::knownWallets())->firstWhere('address', $this->wallet->address);
    }
}
