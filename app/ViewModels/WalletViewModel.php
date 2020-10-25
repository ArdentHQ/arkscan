<?php

declare(strict_types=1);

namespace App\ViewModels;

use App\Facades\Network;
use App\Models\Scopes\EntityRegistrationScope;
use App\Models\Wallet;
use App\Services\Blockchain\NetworkStatus;
use App\Services\ExchangeRate;
use App\Services\NumberFormatter;
use App\Services\Timestamp;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Mattiasgeniar\Percentage\Percentage;
use Spatie\ViewModels\ViewModel;

final class WalletViewModel extends ViewModel
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
        return NumberFormatter::currency($this->wallet->balance / 1e8, Network::currency());
    }

    public function balanceFiat(): string
    {
        return ExchangeRate::convert($this->wallet->balance / 1e8, Timestamp::fromUnix(Carbon::now()->unix())->unix());
    }

    public function balancePercentage(): string
    {
        return NumberFormatter::percentage(Percentage::calculate($this->wallet->balance / 1e8, NetworkStatus::supply()));
    }

    public function nonce(): string
    {
        return NumberFormatter::number($this->wallet->nonce);
    }

    public function votes(): string
    {
        return NumberFormatter::currency($this->wallet->attributes['delegate']['voteBalance'] / 1e8, Network::currency());
    }

    public function votesPercentage(): string
    {
        return NumberFormatter::percentage(Percentage::calculate($this->wallet->attributes['delegate']['voteBalance'] / 1e8, NetworkStatus::supply()));
    }

    public function amountForged(): string
    {
        return NumberFormatter::currency($this->wallet->blocks()->sum('total_amount') / 1e8, Network::currency());
    }

    public function feesForged(): string
    {
        return NumberFormatter::currency($this->wallet->blocks()->sum('total_fee') / 1e8, Network::currency());
    }

    public function rewardsForged(): string
    {
        return NumberFormatter::currency($this->wallet->blocks()->sum('reward') / 1e8, Network::currency());
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

    public function forgedTotal(): string
    {
        return NumberFormatter::currency(floor($this->wallet->blocks()->sum('total_amount') / 1e8), Network::currency());
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
        return NumberFormatter::number(0);
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
        return ViewModelFactory::collection(
            $this->wallet->sentTransactions()->withScope(EntityRegistrationScope::class)->get()
        );
    }

    public function isVoting(): bool
    {
        return ! is_null(Arr::get($this->wallet, 'attributes.vote'));
    }

    public function vote(): ?self
    {
        $wallet = Wallet::where('public_key', Arr::get($this->wallet, 'attributes.vote'))->first();

        if (is_null($wallet)) {
            return null;
        }

        return new static($wallet);
    }

    private function findWalletByKnown(): ?array
    {
        return collect(Network::knownWallets())->firstWhere('address', $this->wallet->address);
    }
}
